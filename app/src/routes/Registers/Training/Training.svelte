<script>
    import { onMount } from "svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";
    import { jspa } from "@shared/jspa.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { toastSuccess } from "@shared/toastHelper.js";

    export let params;

    let training = {};

    let staff_ids = [];
    let stored_staff_ids = [];

    let is_loaded = false;

    let stored_training = Object.assign({}, training);

    let trainingStatusOptions = [
        { option: "Completed", value: "completed" },
        { option: "In Progress", value: "in_progress" },
    ];

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/trainings", name: "Trainings" },
        ]
    });

    let mounted = false;

    onMount(() => {
        if (params.id != "add") {
            jspa("/Register/Training", "getTraining", { id: params.id })
                .then((result) => {
                    training = result.result || { staff_ids: [] };

                    if (training.id) {
                        jspa("/Register/TrainingAssignees", "getTrainingAssignees", { training_id: training.id })
                            .then((result) => {
                                staff_ids = (result.result || []).map(id => parseInt(id, 10));
                                stored_staff_ids = [...staff_ids];
                            })
                            .catch(() => {});
                    }
                })
                .catch(() => {})
                .finally(() => {
                    stored_training = Object.assign({}, training);
                    is_loaded = true;
                });
        }
        mounted = true;
    });

    $: {
        if (mounted) {
            const trainingChanged = JSON.stringify(training) !== JSON.stringify(stored_training);
            const staffIdsChanged = JSON.stringify(staff_ids) !== JSON.stringify(stored_staff_ids);

            ActionBarStore.set({
                can_delete: false,
                show: trainingChanged || staffIdsChanged, // Trigger the action bar on changes
                undo: () => undo(),
                save: () => save(),
            });
        }
    }

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

    function undo() {
        training = Object.assign({}, stored_training);
        staff_ids = [...stored_staff_ids];
    }

    function save() {
        jspa("/Register/Training", "updateTraining", { ...training, staff_ids })
            .then((result) => {
                training = result.result || { staff_ids: [] };
                stored_training = Object.assign({}, training);
                return jspa("/Register/TrainingAssignees", "getTrainingAssignees", { training_id: training.id });
            })
            .then((result) => {
                toastSuccess("Training updated successfully!");toastSuccess
                staff_ids = (result.result || []).map(id => parseInt(id, 10));
                stored_staff_ids = [...staff_ids];
            })
            .catch((error) => {
                console.error("Error updating training:", error);
            });
    }

</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Training Details
</div>

{#if is_loaded}
    <StaffMultiSelector 
        bind:staff_ids={staff_ids}
    /> 
{/if}


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
