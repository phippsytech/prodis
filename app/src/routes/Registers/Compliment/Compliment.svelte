<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { push } from "svelte-spa-router";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    
    export let params;

    let compliment = {};
    let stored_compliment = {};

    let staffer = [];

    let mounted = false;

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/compliments/", name: "Compliments" },
        ]
    });

    onMount(() => {
        if (params.id != "add") {
            // Fetch compliment details from the API
            jspa("/Register/Compliment", "getCompliment", { id: params.id })
                .then((result) => {
                    compliment = result.result;
                    stored_compliment = Object.assign({}, compliment);;
                })
                .catch(() => {
                    toastError("Failed to load compliment.");
                })

        }
        mounted = true;
    });

    $: {
        if (mounted) {
            const complimentChanged = JSON.stringify(compliment) !== JSON.stringify(stored_compliment);

            ActionBarStore.set({
                can_delete: false,
                show: complimentChanged,  // Trigger the action bar on changes
                undo: () => undo(),
                save: () => save(),
            });
        }
    }

    jspa("/Staff", "listStaff", {}).then((result) => {
        staffer = result.result
            .filter((item) => item.archived !== "1")
            .map((item) => ({
                label: `${item.staff_name}`, 
                value: item.id,
            }))
            .sort((a, b) => a.label.localeCompare(b.label));
    });


    function undo() {
        compliment = Object.assign({}, stored_compliment);
    }

    function save() {
        jspa("/Register/Compliment", "updateCompliment", { ...compliment })
            .then((result) => {
                if (result.error) {  
                    toastError(result.error);
                    return;
                }
            })
            .then((result) => {
                push("/registers/compliments");
                toastSuccess("Compliment updated successfully!");
            })
            .catch((error) => {
                console.error("Error updating compliment:", error);
                toastError("Error updating compliment, please try again.");
            });
    }

    function deleteCompliment() {
        if (confirm("Are you sure you want to delete this compliment?")) {
            jspa("/Register/Compliment", "deleteCompliment", { id: compliment.id })
            .then((result) => {
                toastSuccess("Compliment successfully deleted");
                push("/registers/compliments");
            })
            .catch((error) => {
                toastError("Error deleting compliment");
            });
        }
    }

</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2 mt-2"
    style="color:#220055;"
>
    Compliment Details
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

<div class="flex">
    <button 
        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
        on:click="{deleteCompliment}"
        >
        Delete
    </button>
</div>