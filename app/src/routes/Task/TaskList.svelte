<script>
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { getDaysUntilDate } from "@shared/utilities.js";
  import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
  import { push } from "svelte-spa-router";
  import { slide } from "svelte/transition";
  import { flip } from "svelte/animate";

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

{#if taskList.length > 0}
  <div class="flex sm:flex-row flex-col sm:items-center mt-6 mb-1">
    <h3 class="text-slate-800 font-bold mx-2">Your Tasks</h3>
  </div>

  <ul
    class="bg-white rounded-lg border border-indigo-100 w-full text-slate-900"
  >
    {#each taskList as task, index (index)}
      <li
        animate:flip={{ duration: 200 }}
        in:slide={{ duration: 200 }}
        out:slide|local={{ duration: 200 }}
        class="px-4 py-2 hover:bg-indigo-50/50 hover:text-indigo-600 focus:outline-none focus:ring-0 focus:bg-indigo-50/50 focus:text-slate-600 {isOverDue(
          task.due_date
        )
          ? 'text-red-800'
          : ''} transition duration-500 cursor-pointer {taskList.length - 1 ==
        index
          ? 'rounded-b-lg'
          : ''} border-b border-indigo-100 w-full

        {isOverDue(task.due_date) ? 'bg-red-50' : ''}
                  "
      >
        <a
          href="/#/tasks/{task.id}"
          class="flex justify-between items-start text-sm gap-x-4"
        >
          <div>
            {task.title}
          </div>
          {#if task.due_date}
            <div class="whitespace-nowrap text-xs">
              Due {task.due_date ? getDaysUntilDate(task.due_date) : ""}
            </div>
          {/if}
        </a>
      </li>
    {/each}
  </ul>
{/if}
