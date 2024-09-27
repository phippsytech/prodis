<script>
    import { onMount } from "svelte";
    import { push } from "svelte-spa-router";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import { jspa } from "@shared/jspa.js";

    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    export let params;

    let training = {};
    let stored_training = Object.assign({}, training);

    let trainingStatusSelectElement = null;

    let trainingStatusOptions = [
        { option: "Completed", value: "completed" },
        { option: "In Progress", value: "in_progress" },
    ];

    let staff = [];
    let staffList = [];
    let staffSelectElement = null;

    let readOnly = false;

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/trainings", name: "Trainings" },
            { url: "", name: "Training" }
        ]
    });

    jspa("/Staff", "listStaff", {})
        .then((result) => {
            staff = result.result;
            staff.forEach((staffer) => {
                if (staffer.archived != 1)
                    staffList.push({ option: staffer.name, value: staffer.id });
            });
            staffList = staffList;
        })
        .catch(() => {});

    let mounted = false;
    let show = false;

    onMount(() => {
        if (params.id != "add") {
            jspa("/Register/Training", "getTraining", { id: params.id })
                .then((result) => {
                    training = result.result;
                })
                .catch(() => {})
                .finally(() => {
                    // Make a copy of the object
                    stored_training = Object.assign({}, training);
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
                training = result.result;
                stored_training = Object.assign({}, training);
                // let result = result.result.id;
            })
            .catch(() => {});
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(training) === JSON.stringify(stored_training)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }

    $: {
        readOnly = training.status == "closed";
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
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Add training
</div>


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
