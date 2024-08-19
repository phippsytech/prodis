<script>
    import Container from "@shared/Container.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { push } from "svelte-spa-router";
    import { UserStore, BreadcrumbStore } from "@shared/stores.js";

    import StaffSelector from "@app/routes/Billables/StaffSelector.svelte";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import History from "./Tasks.svelte";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    $: userStore = $UserStore;

    // Page metadata
    document.title = "Add Task";
    BreadcrumbStore.set({
        path: [{ url: "/tasks", name: "Tasks" }, { name: "Add Task" }]
    });

    const INITIAL_TASK_STATE = {
        id: null,
        title:"",
        description:"",
        user_id: null,
        status: "pending",
        priority: "normal",
        assigned_to: null,
        due_date: null
    };

    export let task = { ...INITIAL_TASK_STATE };

    $: {   
        task.user_id = userStore.id   
    }

    let status_options = [
        { option: "Pending", value: "pending" },
        { option: "In Progress", value: "in_progress" },
        { option: "Completed", value: "completed" },
        { option: "Overdue", value: "overdue" }
    ];

    let priority_options = [
        { option: "Low", value: "low" },
        { option: "Normal", value: "normal" },
        { option: "High", value: "high" },
        { option: "Urgent", value: "urgent" }
    ];

    function validateTask(task) {
        if (task.title === '') {
            toastError("Please enter a task title");
            return false;
        }
        if (task.description === '') {
            toastError("Please enter a task description");
            return false;
        }
        return true;
    }

    function add(task) {
        if (!validateTask(task)) return;

        if (!task.id) {
            jspa("/Task", "addTask", task)
                .then((result) => {
                    task = result.result;
                    toastSuccess("Task added");

                })
                .catch((error) => {
                    let error_message = error.error_message;

                    toastError(error_message);
                });
        } else {
            jspa("/Task", "updateTask", task)
                .then((result) => {
                    task = result.result;
                    toastSuccess("Task updated");

                })
                .catch((error) => {
                    let error_message = error.error_message;

                    toastError(error_message);
                });
        }
    }

    let show = false;
    $: {
        if (task.title != '' && task.description != '') {
            show = true;
        } else {
            show = false;
        }
        
        ActionBarStore.set({
            can_delete: false,
            show: show,
            save: () => add(task),
        });
    }
</script>


<FloatingInput label="Task" type="text" bind:value={task.title} />
<FloatingTextArea
    label="Description"
    placeholder="Describe the task"
    type="text"
    bind:value={task.description}
/>

{#if task.id != null}
    <div class="flex gap-2">
        <FloatingDate label="Due Date" bind:value={task.due_date} />
        <FloatingSelect label="Status" bind:value={task.status} options={status_options} />
        <FloatingSelect label="Priority" bind:value={task.priority} options={priority_options} />
        <div class="w-1/3">
            <StaffSelector bind:staff_id={task.assigned_to} />
        </div>
    </div>
{/if}