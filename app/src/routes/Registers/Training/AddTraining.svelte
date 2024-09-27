<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";

    let training = {
        course_title: "",
        trainer: "",
        date: null,
        completion_date: null,
        status: null, // Default status
        staff_ids: [] // Selected staff IDs
    };

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

    function addTraining() {
        jspa("/Register/Training", "addTraining", training)
            .then((result) => {
                let training_id = result.result.id;
                push("/registers/trainings");
            })
            .catch(() => {});
    }
</script>
<StaffMultiSelector bind:staff_ids={training.staff_ids}/> 

<FloatingInput
    bind:value={training.course_title}
    label="Course Title"
    placeholder="Title of the training course"
/>

<FloatingInput
    bind:value={training.trainer}
    label="Trainer"
    placeholder="John Doe"
/>

<FloatingDate label="Training Date" bind:value={training.date} />

<FloatingDate label="Training Completion Date" bind:value={training.completion_date} />

<label class="block mb-2">
    <span class="text-xs opacity-50 p-0 m-0 block mb-2">Training Status</span>
    <RadioButtonGroup
        columns={2}
        options={trainingStatusOptions}
        bind:value={training.status}
    />
</label>

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addTraining()} label="Add training" />
</div>
