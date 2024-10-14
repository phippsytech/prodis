<script>
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";
    import RTE from "@shared/RTE/RTE.svelte";
    import Role from "@shared/Role.svelte";
    import ComplimentForm from "./ComplimentForm.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";

    let compliment = {};

    let showActionFields = false;

    compliment.acknowledgement_date = null;
    compliment.status = "not_acknowledged";

    let staffer = [];

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/compliments/", name: "Compliments" },
        ]
    });

    jspa("/Staff", "listStaff", {}).then((result) => {
        staffer = result.result
            .filter((item) => item.archived !== "1")
            .map((item) => ({
                label: `${item.staff_name}`, 
                value: item.id,
            }))
            .sort((a, b) => a.label.localeCompare(b.label));
    });

    const validations = [
        { check: () => !compliment.date, message: "Date of compliment must be provided." },
        { check: () => !compliment.complimenter, message: "Complimenter name must be provided." },
        { check: () => !compliment.description, message: "Description must be provided." },
        {
            check: () => {
                const currentDate = new Date();
                const complimentDate = new Date(compliment.date);
                return complimentDate > currentDate;
            },
            message: "Compliment date cannot be in the future."
        }
    ];

    function validate() {
        for (const { check, message } of validations) {
            if (check()) {
                toastError(message);
                return false;
            }
        }
        return true;
    }

    function addCompliment() {
        if (validate()) {
            if (compliment.action_taken && compliment.staffs_id) {
                compliment.status = "acknowledged";
                
                const today = new Date();
                compliment.acknowledgement_date = today.toISOString().split('T')[0];
            }
            
            jspa("/Register/Compliment", "addCompliment", compliment)
                    .then(() => {
                    push("/registers/compliments");
                    toastSuccess("Compliment added successfully");
                })
                .catch(() => {
                    toastError("Failed to add compliment");
                });
        }
    }

</script>

<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
    <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
        Add Compliment
    </h1>
</div>

<ComplimentForm 
    bind:compliment={compliment}
    staffer={staffer}
    bind:showActionFields
/>

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addCompliment()} label="Add Compliment" />
</div> 
