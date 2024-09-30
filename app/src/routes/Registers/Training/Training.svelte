<script>
    import { onMount } from "svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";
    import { jspa } from "@shared/jspa.js";
    import { push } from "svelte-spa-router";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";

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
                can_delete: true,
                show: trainingChanged || staffIdsChanged, // Trigger the action bar on changes
                undo: () => undo(),
                save: () => save(),
                delete: () => deleteTraining(),
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



    function deleteTraining() {
        jspa("/Register/Training", "deleteTraining", { id: training.id })
            .then((result) => {
                push("/registers/trainings");
                toastSuccess("Training successfully deleted");
            })
            .catch((error) => {
                toastError("Error deleting training");
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
        <FloatingDate label="Training start date" bind:value={training.date} />
    </div>
    <div class="flex-1">
        <FloatingDate label="Training completion date" bind:value={training.completion_date} />
    </div>
    <div class="flex-1">
        <FloatingSelect
            bind:value={training.status}
            label="Status"
            instruction="Training status"
            options={trainingStatusOptions}
            hideValidation={true}
        />
    </div>
</div>

