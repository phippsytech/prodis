<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import NewFloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";

    let training = {};

    let clearable = true;

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/trainings/", name: "Training" },
            { url: "/registers/trainings/add", name: "Add Training" },
        ]
    });


    let trainingStatusOptions = [
        { option: "Completed", value: "completed" },
        { option: "In Progress", value: "in_progress" },
    ];

    // update status based on completion date
    $: if (training.completion_date) {
        const currentDate = new Date();
        const completionDate = new Date(training.completion_date);

        if (completionDate <= currentDate) {
            training.status = "completed"; 
        } else {
            training.status = "in_progress"; 
        }
    }

    $: if (training.date && training.completion_date && new Date(training.date) > new Date(training.completion_date)) {
        toastError("The training start date should not be greater than the completion date.");
    }

    function addTraining() {
        if (training.date && training.completion_date && new Date(training.date) <= new Date(training.completion_date)) {
            jspa("/Register/Training", "addTraining", training)
                .then((result) => {
                    let training_id = result.result.id;
                    push("/registers/trainings");
                    toastSuccess("Training added successfully");
                })
                .catch((error) => {
                    toastError("Failed to add training");
                });
        } else {
            toastError("Please ensure that the training start date is not greater than the completion date.");
        }
    }
</script>
<StaffMultiSelector bind:staff_ids={training.staff_ids}/> 

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
            {clearable} 
        />
    </div>
    <div class="flex-1">
        <FloatingDate 
            label="Training completion date" 
            bind:value={training.completion_date} 
            {clearable}
        />
    </div>
</div>

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addTraining()} label="Add training" />
</div>
