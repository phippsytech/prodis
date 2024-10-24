<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore, UserStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import ContinuousImprovementForm from './ContinuousImprovementForm.svelte';
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";

    let continuous_improvement = {};

    continuous_improvement.user_id = $UserStore.id;
    
    let staffer = [];

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/continuousimprovements/", name: "Continuous Improvements" },
        ]
    });

    const validations = [
        { check: () => !continuous_improvement.involved_staffs_id || continuous_improvement.involved_staffs_id.length === 0, 
            message: "Please select at least one involved staff member." 
        },
        { check: () => !continuous_improvement.involved_staffs_id || continuous_improvement.involved_staffs_id.length === 0, 
            message: "Please select at least one involved staff member." 
        },
        { check: () => !continuous_improvement.date_of_suggestion, 
            message: "Suggestion date must be provided." 
        },
        { check: () => !continuous_improvement.suggestion_details, 
            message: "Suggestion details must be provided." 
        },
        { check: () => continuous_improvement.involved_staffs_id === continuous_improvement.implementing_staffs_id,
            message: "Involved staff cannot be the same as implementing staff." 
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

    jspa("/Staff", "listStaff", {}).then((result) => {
        staffer = result.result
            .filter((item) => item.archived !== "1")
            .map((item) => ({
                label: `${item.staff_name}`, 
                value: item.id,
            }))
            .sort((a, b) => a.label.localeCompare(b.label));
    });


    // add create function
    function addContinuousImprovement() {
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
                
            jspa("/Register/ContinuousImprovement", "addContinuousImprovement", continuous_improvement)
                    .then(() => {
                    push("/registers/continuousimprovements");
                    toastSuccess("Continuous improvement added successfully");
                })
                .catch(() => {
                    toastError("Failed to add continuous improvement");
            });
        }
    }
</script>

<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
    <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
        Add Continuous Improvement
    </h1>
</div>

<ContinuousImprovementForm 
    bind:continuous_improvement={continuous_improvement}
    bind:staffer={staffer}
/>

<div class="flex justify-between mt-2">
    <span></span>
    <Button on:click={addContinuousImprovement} label="Add Continuous Improvement" />
</div>