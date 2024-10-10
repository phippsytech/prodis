<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { push } from "svelte-spa-router";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import ContinuousImprovementForm from './ContinuousImprovementForm.svelte';

    export let params; 

    let continuous_improvement = {};
    let stored_value = {};
    let showActionFields = false;

    let staffer = [];

    let mounted = false; 

    onMount(() => {
        if (params.id != "add") {
            jspa("/Register/ContinuousImprovement", "getContinuousImprovement", { id: params.id })
                .then((result) => {
                    continuous_improvement = result.result;
                    stored_value = Object.assign({}, continuous_improvement);
                    showActionFields = !!continuous_improvement.action_taken;
                }).finally(() => {
                })
                .catch((error) => {
                    console.log(error);
                    toastError("Failed to load continuous improvement.");
                })

        }
        mounted = true;
    });

    $: {
        if (mounted) {
            const valueChanged = JSON.stringify(continuous_improvement) !== JSON.stringify(stored_value);

            ActionBarStore.set({
                can_delete: true,
                show: true,  // Trigger the action bar on changes
                undo: () => undo(),
                save: () => save(),
                delete: () => deleteContinuousImprovement()
            });
        }
    }

    // $: {
    //     if (!showActionFields) {
    //         // implementation_date implementing_staffs_id action_taken review_date completion_date impact_analysis
    //         continuous_improvement.implementation_date = null;
    //         continuous_improvement.implementing_staffs_id = null;
    //         continuous_improvement.action_taken = null;
    //         continuous_improvement.review_date = null;
    //         continuous_improvement.completion_date = null;
    //         continuous_improvement.impact_analysis = null;
    //         continuous_improvement.status = "in_progress";
    //     }
    // }

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
        { check: () => !continuous_improvement.involved_staffs_id || continuous_improvement.involved_staffs_id.length === 0, message: "Please select at least one involved staff member." },
        { check: () => !continuous_improvement.involved_staffs_id || continuous_improvement.involved_staffs_id.length === 0, message: "Please select at least one involved staff member." },
        { check: () => !continuous_improvement.date_of_suggestion, message: "Suggestion date must be provided." },
        { check: () => !continuous_improvement.suggestion_details, message: "Suggestion details must be provided." },
    ];

    function validate(){
        for (const { check, message } of validations) {
            if (check()) {
                toastError(message);
                return false;
            }
        }
        return true;
    }

    function undo() {
        continuous_improvement = Object.assign({}, stored_value);
        showActionFields = continuous_improvement.action_taken  ? true : false;
    }

    function save() {
        if (validate()) {
            const today = new Date();
            const current_date = today.toISOString().split('T')[0]; 
            
            continuous_improvement.status = 
                (continuous_improvement.action_taken && continuous_improvement.implementing_staffs_id)
                ? (continuous_improvement.implementation_date = continuous_improvement.implementation_date || current_date, "implemented")
                : "in_progress";

            continuous_improvement.status = 
                (continuous_improvement.review_date && continuous_improvement.impact_analysis)
                ? (continuous_improvement.completion_date = continuous_improvement.completion_date || current_date, "completed")
                : continuous_improvement.status;

            jspa("/Register/ContinuousImprovement", "updateContinuousImprovement", { ...continuous_improvement })
            .then((result) => {
                if (result.error) {
                    toastError(result.error);
                    return;
                }
                push("/registers/continuousimprovements");
                toastSuccess("Continuous improvement updated successfully!");
            })
            .catch(() => {
                toastError("Error updating continuous improvement, please try again.");
            });
        }
    }

    function deleteContinuousImprovement() {
        if (confirm("Are you sure you want to delete this continuous improvement?")) {
            jspa("/Register/ContinuousImprovement", "deleteContinuousImprovement", { id:  continuous_improvement.id })
            .then((result) => {
                toastSuccess("Continuous improvement successfully deleted");
                push("/registers/continuousimprovements");
            })
            .catch(() => {
                toastError("Error deleting  continuous improvement");
            });
        }
    }

    //TODO: how can we change the status for this register
    //actions taken, implementation date, implementing staff = "implemented status"?
    //if there is actions taken or done and implementing staff, we should set the implementation date to where we added the action taken
    //so we dont need the implementation date field in the form
    //implementation date should not be less than the date of suggestion as it should be in the future

    //review date, impact analysis, completion date = "completed status" ?
    //if the action taken is reviewed and the impact analysis is done, then we set the completion date to the current date in which 
    //those 2(review date and impact analysis) are saved in the database
    //review date should not be greater than the completion date

    //maybe we should put another checkbox in the action fields area
    //if we check the action taken was reviewed check box, then we display the other portion of the input fields?

</script>
<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2" style="color: rgb(34, 0, 85);">
    Continuous Improvement Details
</div>

<ContinuousImprovementForm 
    bind:continuous_improvement={continuous_improvement}
    bind:staffer={staffer}
    bind:showActionFields={showActionFields}
/>
<!-- 
<Role roles={["admin"]}>
    <div class="flex">
        <button 
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
            on:click="{deleteContinuousImprovement}"
            >
            Delete
        </button>
    </div>
</Role>  -->
