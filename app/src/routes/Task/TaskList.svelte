<script>
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { getDaysUntilDate } from "@shared/utilities.js";
  import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
  import { push } from "svelte-spa-router";

  $: stafferStore = $StafferStore;

  let taskList = [];
  let tasks = [];
  let search = "";

  async function loadTaskList() {
    jspa("/Task", "listTasks", { staff_id: stafferStore.id }).then((result) => {
      tasks = result.result;
    });
  }

  function isOverDue(date) {
    if (!date) return false;

    return new Date(date) < new Date();
  }

  onMount(async () => {
    await loadTaskList();
  });

  $: {
    taskList = tasks.filter(
      (task) => task.title.toLowerCase().includes(search.toLowerCase()) == true
    );
  }
</script>

<div class="flex sm:flex-row flex-col sm:items-center mt-6 mb-1">
  <h3 class="text-slate-800 font-bold mx-2">Your Tasks</h3>
</div>

<div
  class="bg-white rounded-lg border border-indigo-100 w-full text-slate-800 p-4"
>
  {#if taskList.length > 0}
    <table class="w-full">
      <tr class="border-b-2">
        <th class="text-left">Task</th>
        <th class="text-right">Due Date</th>
      </tr>

      {#each taskList as task, index (index)}
        <tr
          class="border-b hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {isOverDue(
            task.due_date
          )
            ? 'bg-red-500 text-white'
            : ''}"
          on:click={() => push("/tasks/" + task.id)}
        >
          <td class="font-semibold p-2">{task.title}</td>
          <td class="text-right"
            >{task.due_date ? getDaysUntilDate(task.due_date) : ""}</td
          >
        </tr>
      {/each}
    </table>
  {:else}
    <div class="text-sm text-center text-slate-500 p-4 pt-2">
      <div
        class="flex justify-center items-center h-8 w-8 text-slate-300 mx-auto m-2"
      >
        <svg
          data-slot="icon"
          fill="none"
          stroke-width="1.5"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
          aria-hidden="true"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"
          ></path>
        </svg>
      </div>
      <div>No Tasks found</div>
    </div>
  {/if}
</div>
