<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";

    let training = {};

    let evidenceOptions = [
        { option: "Yes", value: "yes" },
        { option: "No", value: "no" },
    ];

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/trainings/", name: "Trainings" },
        ]
    });

    // Update status based on completion date
    $: {
        const currentDate = new Date();

        if (training.completion_date) {
            const completionDate = new Date(training.completion_date);
            training.status = completionDate <= currentDate ? "completed" : "in_progress"; 
        } else {
            training.status = "in_progress"; // Default status if no completion date
        }
    }

    const validations = [
        { check: () => !training.staff_ids || training.staff_ids.length === 0, message: "Please select at least one staff member." },
        { check: () => !training.course_title, message: "Course title must be provided." },
        { check: () => !training.trainer, message: "Trainer must be provided." },
        { check: () => !training.date, message: "Training start date must be provided." },
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

    function addTraining() {
        if (!validate()) {
            return; 
        }
        if (training.date) {
            if (training.completion_date) {
                    if (new Date(training.date) > new Date(training.completion_date)) {
                        toastError("The training start date should not be greater than the completion date.");
                        return;
                    }
            }

            jspa("/Register/Training", "addTraining", training)
                .then(() => {
                push("/registers/trainings");
                toastSuccess("Training added successfully");
            })
            .catch(() => {
                toastError("Failed to add training");
            });
        } else {
            toastError("Please enter a training start date.");
        }
    }
</script>

<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
    <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
        Add Training
    </h1>
</div>

<StaffMultiSelector bind:staff_ids={training.staff_ids} /> 

<FloatingInput
    bind:value={training.course_title}
    label="Course title"
    placeholder="Title of the training course"
/>

<FloatingInput
    bind:value={training.trainer}
    label="Trainer"
    placeholder="John Doe"
/>

<div class="flex space-x-4 w-full">
    <div class="flex-1"> 
        <FloatingDate 
            label="Training start date" 
            bind:value={training.date}
        />
    </div>
    <div class="flex-1">
        <FloatingDate 
            label="Training completion date" 
            bind:value={training.completion_date}
        />
    </div>
</div>

<!-- <Role roles={["admin"]}>
    {#if training.status === "completed"}
        <NewFloatingSelect
        on:change
        bind:value={training.has_evidence}
        label="Training evidence"
        instruction="If training has evidence of completion"
        options={evidenceOptions}
        />
    {/if}
</Role> -->

<div class="flex justify-between">
    <span></span>
    <Button on:click={addTraining} label="Add Training" />
</div>
