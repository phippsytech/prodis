<script>
    import { onMount } from "svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";
    import { jspa } from "@shared/jspa.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    export let params;

    let training = {};
    let training_assignees = {};

    let staff_ids = [];

    let stored_training = Object.assign({}, training);
    let trainingStatusOptions = [
        { option: "Completed", value: "completed" },
        { option: "In Progress", value: "in_progress" },
    ];

    let staffList = [];

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/trainings", name: "Trainings" },
            { url: "", name: training.course_title }
        ]
    });

    let mounted = false;

    onMount(() => {
        if (params.id != "add") {
            jspa("/Register/Training", "getTraining", { id: params.id })
                .then((result) => {
                    training = result.result || { staff_ids: [] };
                })
                .catch(() => {})
                .finally(() => {
                    stored_training = Object.assign({}, training);

                    if (training.id) {
                        jspa("/Register/TrainingAssignees", "getTrainingAssignees", { training_id: training.id })
                            .then((result) => {
                                training_assignees = result.result || { staff_ids: [] };
                                staff_ids = training_assignees.staff_ids;
                            })
                            .catch(() => {});
                    }
                });
        }
        mounted = true;
    });

    function undo() {
        training = Object.assign({}, stored_training);
    }

    function save() {
        jspa("/Register/Training", "updateTraining", training)
            .then((result) => {
                training = result.result || { staff_ids: [] };
                stored_training = Object.assign({}, training);

                return jspa("/Register/Training", "getTrainingAssignees", { id: training.id });
            })
            .then((result) => {
                training.staff_ids = result.result || [];
                console.log(training.staff_ids);
            })
            .catch(() => {});
    }
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Training Details
</div>

<StaffMultiSelector bind:staff_ids={staff_ids}/> 

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
