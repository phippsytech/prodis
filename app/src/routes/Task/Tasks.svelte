<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";
    import { formatDate } from "@shared/utilities.js";

    export let staff_id;

    let tasks = [];

    let display = "pending";

    onMount(() => {
        listTrips();
    });

    export function listTrips() {
        if (staff_id == null) return;
        jspa("/Trip", "listTrips", { staff_id: staff_id }).then((result) => {
            tasks = result.result;
        });
    }

    function deleteTrip(task_id) {
        jspa("/Trip", "deleteTrip", { id: task_id }).then((result) => {
            tasks = tasks.filter((task) => task.id !== task_id);
            // tasks = result.result
        });
    }

    tasks = [
        {
            task: "Special Task",
            description: "This is a special task just for Phippsy to complete",
            created_by: "Phippsy",
            assigned_to: "Phippsy",
            due_date: "2021-09-30",
            due_time: "12:00",
        },
        {
            task: "Special Task",
            description: "This is a special task just for Phippsy to complete",
            created_by: "Phippsy",
            assigned_to: "Phippsy",
            due_date: "2021-09-30",
            due_time: "12:00",
        },
        {
            task: "Special Task",
            description: "This is a special task just for Phippsy to complete",
            created_by: "Phippsy",
            assigned_to: "Phippsy",
            due_date: "2021-09-30",
            due_time: "12:00",
        },
    ];
</script>

{#if tasks.length > 0}
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
                    >Add Task</button
                >
            </div>
        </div>

        <table class="w-full">
            <tr class="border-b-2">
                <th class="text-left">Task</th>
                <th class="text-left">Assigned to</th>
                <th class="text-right">Due</th>
            </tr>

            {#each tasks as task}
                <tr class="border-b">
                    <td class="font-semibold p-2">{task.task}</td>
                    <td>{task.assigned_to}</td>
                    <td class="text-right">{formatDate(task.due_date)}</td>
                </tr>
            {/each}
        </table>
    </Container>
{/if}
