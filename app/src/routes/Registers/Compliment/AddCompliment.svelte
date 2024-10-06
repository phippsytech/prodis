<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";

    let compliment = {};

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

    function addCompliment() {
        if (compliment.action_taken) {
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
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2 mt-2"
    style="color:#220055;"
>
    Add Compliment
</div>

<FloatingDate
    bind:value={compliment.date}
    label="Date of compliment"
/>

<div class="flex space-x-4">
    <div class="flex-1">
        <FloatingInput 
            bind:value={compliment.complimenter}
            label="Name of complimenter"
            placeholder="eg: Eva Snow"
        />
    </div>

    <div class="flex-1">
        <FloatingCombo 
            bind:value={compliment.staffs_id}
            items={staffer}
            label="Select Staff"
            placeholderText="Select or type staff name"
        />
    </div>
</div>

<RTE 
    bind:content={compliment.description}
/>

<div class="mt-2">
    <FloatingTextArea 
    bind:value={compliment.action_taken}
    label="Action Taken"
    placeholder=" Indicate action taken by staff"
/> 
</div>


<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addCompliment()} label="Add Compliment" />
</div> 
