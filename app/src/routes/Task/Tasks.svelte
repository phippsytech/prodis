<script>
  import { getStaffer } from "@shared/api.js";
  import { onMount } from "svelte";
  import Container from "@shared/Container.svelte";
  import { jspa } from "@shared/jspa.js";
  import {
    getDaysUntilDate,
    formatDateTime,
    formatPrettyName,
  } from "@shared/utilities.js";
  import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
  import { push } from "svelte-spa-router";
  import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";
  import { writable } from "svelte/store";

  document.title = "Tasks";
  BreadcrumbStore.set({
    path: [{ url: "/tasks", name: "Tasks" }],
  });

  let showArchived = false;
  let taskList = [];
  let tasks = [];
  let search = "";

  async function loadTaskList() {
    jspa("/Task", "listTasks", {}).then((result) => {
      tasks = result.result;
    });
  }

  onMount(async () => {
    await loadTaskList();
  });

  $: {
    taskList = tasks.filter(
      (task) => task.title.toLowerCase().includes(search.toLowerCase()) == true
    );

    if (!showArchived) {
      taskList = taskList?.filter((task) => task.archived != 1);
    }
  }
</script>

<Container>
  <div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
      <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
      >
        Tasks
      </h1>
    </div>
  </div>
  <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-2">
    <div class="relative flex flex-1">
      <label for="search-field" class="sr-only">Search</label>
      <svg
        class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
          clip-rule="evenodd"
        />
      </svg>
      <input
        bind:value={search}
        id="search-field"
        class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
        placeholder="search by task name..."
        type="search"
        name="search"
      />
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
      <button
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        on:click={() => push("/tasks/add")}>Add Task</button
      >
    </div>
  </div>
  <label class="text-xs text-gray-400 flex justify-end mt-2">
    <input type="checkbox" bind:checked={showArchived} class="mr-2" />
    Include archived
  </label>
  <table class="w-full">
    <tr class="border-b-2">
      <th class="text-left">Task</th>
      <th class="text-left">Assigned to</th>
      <!-- <th class="text-left">Priority</th> -->
      <th class="text-left">Status</th>
      <th class="text-left">Created By</th>
      <th class="text-right">Due Date</th>
    </tr>

    {#each taskList as task, index (index)}
      <tr
        class="border-b hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer"
        style="opacity: {task.archived == 1 ? 0.5 : 1}"
        on:click={() => push("/tasks/" + task.id)}
      >
        <td class="font-semibold p-2">{task.title}</td>
        <td>
          {#if task.assigned_staff_name || task.assigned_staff_user_name}
            {task.assigned_staff_name ?? task.assigned_staff_user_name}
          {:else}
            Unassigned
          {/if}
        </td>
        <!-- <td>{task.priority ? formatPrettyName(task.priority) : ''}</td> -->
        <td>{task.status ? formatPrettyName(task.status) : ""}</td>
        <td>{task.creator_name}</td>
        <td class="text-right"
          >{task.due_date ? getDaysUntilDate(task.due_date) : ""}</td
        >
      </tr>
    {/each}
  </table>
</Container>
