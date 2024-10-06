<script>
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";
    import RTE from "@shared/RTE/RTE.svelte";
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
            { url: "/registers/compliments/add", name: "Add Compliment" },
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

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2 mt-2"
    style="color:#220055;"
>
    Add Compliment
</div>

<div class="flex space-x-4">
    <div class="flex-1">
        <FloatingInput 
            bind:value={compliment.complimenter}
            label="Name of complimenter"
            placeholder="eg: Eva Snow"
        />
    </div>

    <div class="flex-1">
        <FloatingDate
            bind:value={compliment.date}
            label="Date of compliment"
        />
    </div>
</div>

<span class="ml-2 text-xs text-gray-900/50">Compliment</span>
<RTE 
    bind:content={compliment.description}
/>

<div class="mt-2">
    <label class="inline-flex items-center">
        <input type="checkbox" bind:checked={showActionFields} class="form-checkbox" />
        <span class="ml-2 text-xs">Action was taken for this compliment</span>
    </label>
</div>

{#if showActionFields}
    <div class="mt-2">
        <FloatingTextArea 
        bind:value={compliment.action_taken}
        label="Action Taken"
        placeholder=" Indicate action taken by staff"
        /> 

        <FloatingCombo 
                bind:value={compliment.staffs_id}
                items={staffer}
                label="Acknowledging Staff"
                placeholderText="Select or type staff name"
        />
    </div>
{/if}    


<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addCompliment()} label="Add Compliment" />
</div> 
