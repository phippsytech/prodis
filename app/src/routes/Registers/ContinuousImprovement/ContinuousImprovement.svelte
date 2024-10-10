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

    let staffer = [];

    let mounted = false; 

    
    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/continuousimprovements/", name: "Continuous Improvement" },
        ]
    });

    onMount(() => {
        if (params.id != "add") {
            jspa("/Register/ContinuousImprovement", "getContinuousImprovement", { id: params.id })
                .then((result) => {
                    continuous_improvement = result.result;
                    stored_value = Object.assign({}, continuous_improvement);
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
        { 
            check: () => continuous_improvement.review_date && !continuous_improvement.implementing_staffs_id, 
            message: "Reviewer is required when review date is provided." 
        },
        { 
            check: () => continuous_improvement.implementing_staffs_id && !continuous_improvement.review_date, 
            message: "Review date is required when there is a reviewer." 
        }
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
    }

    $: 
    {
        if (continuous_improvement.implementing_staffs_id == null) {
            continuous_improvement.implementing_staffs_id = null;
        }
    }

    function save() {
        if (validate()) {
            const today = new Date();
            const current_date = today.toISOString().split('T')[0]; 
            
            continuous_improvement.status = 
                (continuous_improvement.action_taken && continuous_improvement.implementing_staffs_id)
                ? (continuous_improvement.implementation_date = continuous_improvement.implementation_date || current_date, "for_review")
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

</script>
<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2" style="color: rgb(34, 0, 85);">
    Continuous Improvement Details
</div>

<ContinuousImprovementForm 
    bind:continuous_improvement={continuous_improvement}
    bind:staffer={staffer}
/>