<script>
    import Container from "@shared/Container.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import ClientSelector from "@app/routes/Billables/ClientSelector.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import History from "./Tasks.svelte";
    import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";

    let history;

    let task = {
        client_id: null,
        staff_id: $StafferStore.id,
        task_date: new Date().toISOString().split("T")[0],
        vehicle_type: "private",
        kms: null,
    };

    let tasks = [];

    console.log(task);

    BreadcrumbStore.set({
        path: [{ url: null, name: "Task Tracking" }],
    });

    function add(task) {
        // console.log(task)
        if (task.client_id == "Choose client" || task.client_id == null) {
            toastError("Please select a client");
            return;
        }

        if (!task.kms || isNaN(task.kms)) {
            toastError("Please enter a valid number of KMs");
            return;
        }
        jspa("/Task", "addTask", task)
            .then((result) => {
                task = result.result;
                console.log(result);

                toastSuccess("Task added");
                history.listTasks();
            })
            .catch((error) => {
                let error_message = error.error_message;

                toastError(error_message);
            });
    }

    let assigned_to;

    let staff = [
        { value: 1, option: "John Doe" },
        { value: 2, option: "Jane Doe" },
        { value: 3, option: "John Smith" },
        { value: 4, option: "Jane Smith" },
    ];

    let get_due_date;
</script>

<Container>
    <div
        class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
        style="color:#220055;"
    >
        Add Task
    </div>

    <FloatingInput label="Task" type="text" bind:value={task.task} />
    <FloatingTextArea
        label="Description"
        placeholder="Provide a short description of the task if it helps provide context (optional)"
        type="text"
        bind:value={task.task}
    />

    <FloatingSelect
        label="Assigned To"
        options={staff}
        bind:value={task.assigned_to}
    />

    <Container>
        <div class="mb-2">
            <Toggle
                bind:value={get_due_date}
                label_on="Set Due Date"
                label_off="Set Due Date"
            />
        </div>

        {#if get_due_date}
            <FloatingDate bind:value={task.due_date} label="Date" />
            <FloatingTime bind:value={task.due_time} label="Time" />
        {/if}
    </Container>

    <div class="flex justify-end">
        <button
            on:click={() => add(task)}
            type="button"
            class="rounded-md bg-indigo-600 px-5 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
            >Add Task</button
        >
    </div>
</Container>
