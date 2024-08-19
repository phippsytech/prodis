<script>
	import { getStaffer } from '@shared/api.js';
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";
    import { formatDate } from "@shared/utilities.js";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";
    import createStore from "@shared/createStore";
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";

    document.title = "Tasks";
    BreadcrumbStore.set({
        path: [{ url: "/tasks", name: "Tasks" }]
    });

    onMount(async () => {
        tasks.load();
    });

    // Create ClientReports store
    let tasks = createStore(
        "/Task",
        {
            add: "addTask",
            update: "updateTask",
            list: "listTasks"
        },
        {
            load: (results) => {
                return convertFieldsToBoolean(results, ["is_done"]);
            },
            add: (result) => {
                let resultReport = convertFieldsToBoolean(result, ["is_done"]);
                delete resultReport.update;
                delete resultReport.created;
                delete resultReport.archived;
                return resultReport;
            },
        },
    );

    function formatPrettyName(str) {
        return str?.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }
</script>

    <Container>
        <div class="sm:flex sm:items-center mb-4">
            <div class="sm:flex-auto">
                <h1
                    class="text-2xl font-fredoka-one-regular"
                    style="color:#220055;"
                >
                    Tasks
                </h1>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <button
                    type="button"
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    on:click={() => push("/tasks/add")}
                    >Add Task</button
                >
            </div>
        </div>

        <table class="w-full">
            <tr class="border-b-2">
                <th class="text-left">Task</th>
                <th class="text-left">Assigned to</th>
                <th class="text-left">Priority</th>
                <th class="text-left">Status</th>
                <th class="text-left">Created By</th>
                <th class="text-right">Due Date</th>
            </tr>

            {#each $tasks as task, index (index)}
                <tr 
                    class="border-b hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer"
                    on:click={() => push("/tasks/" + task.id)}>
                    <td class="font-semibold p-2">{task.title}</td>
                    <td>{task.assigned_staff_name ?? ''}</td>
                    <td>{task.priority ? formatPrettyName(task.priority) : ''}</td>
                    <td>{task.status ? formatPrettyName(task.status) : ''}</td>
                    <td>{task.creator_name}</td>
                    <td class="text-right">{formatDate(task.due_date) ?? ''}</td>
                </tr>
            {/each}
        </table>
    </Container>
