<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingDateTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateTime.svelte";
  import StaffSelector from "@app/routes/Billables/StaffSelector.svelte";
  import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
  import RTE from "@shared/RTE/RTE.svelte";
  import RTEView from "@shared/RTE/RTEView.svelte";
  import { BreadcrumbStore, UserStore, StafferStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { formatDateTime, formatPrettyName } from "@shared/utilities.js";
  import AddComment from "./AddComment.svelte";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { push } from "svelte-spa-router";

  //   temporary static components
  import Assignment from "../Tickets/Activity/Assignment.svelte";
  import Comment from "../Tickets/Activity/Assignment.svelte";
  import Time from "../Tickets/Activity/Assignment.svelte";

  // props
  export let params;

  // Page metadata
  document.title = "View Task";
  BreadcrumbStore.set({
    path: [{ url: "/tasks", name: "Tasks" }, { name: "View Task" }],
  });

  $: userStore = $UserStore;
  $: stafferStore = $StafferStore;

  let task_id = params.task_id;

  let isEditingDescription = false;
  let isEditingTitle = false;
  let stored_task = {};

  getTask(task_id);

  let task = null;

  let status_options = [
    { option: "Pending", value: "pending" },
    { option: "In Progress", value: "in_progress", is_user: true },
    { option: "Review", value: "review", is_user: true },
    { option: "Completed", value: "completed" },
  ];

  let priority_options = [
    { option: "Low", value: "low" },
    { option: "Normal", value: "normal" },
    { option: "High", value: "high" },
    { option: "Urgent", value: "urgent" },
  ];

  function undo() {
    task = Object.assign({}, stored_task);
    isEditingDescription = false;
    isEditingTitle = false;
  }

  function getTask(task_id) {
    jspa("/Task", "getTask", { id: task_id })
      .then((result) => {
        task = result.result;
      })
      .catch((error) => {
        let error_message = error.error_message;
        toastError(error_message);
      })
      .finally(() => {
        // Make a copy of the object
        stored_task = Object.assign({}, task);
      });
  }

  function updateTask(task) {
    if (task) {
      // update in_progress_at and in_progress_by if status is changed to in_progress
      if (task.status === "in_progress" && stored_task.status != task.status) {
        const now = new Date().toISOString().slice(0, 19).replace("T", " ");
        task.in_progress_at = now;
        task.in_progress_by = userStore.id;
      }

      if (stored_task.assigned_to != task.assigned_to) {
        task.assigned_by = userStore.id;
      }

      // format due date when updating
      if (task.due_date) {
        task.due_date = task.due_date
          ? new Date(task.due_date).toISOString().slice(0, 19).replace("T", " ")
          : "";
      }

      jspa("/Task", "updateTask", task)
        .then((result) => {
          // the update result is returning null given that it was coming from the basecontroller
          //task = result.result;

          isEditingDescription = false;
          isEditingTitle = false;

          toastSuccess("Task updated successfully");
        })
        .catch((error) => {
          let error_message = error.error_message;
          toastError(error_message);
        })
        .finally(() => {
          // Make a copy of the object
          stored_task = Object.assign({}, task);
        });
    }
  }

  function archiveTask() {
    jspa("/Task", "archiveTask", task).then((result) => {
      // task = result.result;
      // Make a copy of the object
      // stored_task = Object.assign({}, task);
      push("/tasks");
      toastError("Task archived successfully");
    });
  }

  function restoreTask() {
    jspa("/Task", "restoreTask", task).then((result) => {
      task = result.result;
      // Make a copy of the object
      stored_task = Object.assign({}, task);

      push("/tasks");
      toastSuccess("Task restored successfully");
    });
  }

  let show = false;
  let taskCreator = false;
  let taskAssignee = false;
  $: {
    if (userStore.id == task?.user_id) {
      taskCreator = true;
    }

    if (stafferStore.id == task?.assigned_to) {
      taskAssignee = true;
    }

    if (
      task?.title != stored_task?.title ||
      task?.description != stored_task?.description ||
      task?.status != stored_task?.status ||
      task?.due_date != stored_task?.due_date ||
      task?.assigned_to != stored_task?.assigned_to ||
      task?.priority != stored_task?.priority
    ) {
      show = true;
    } else {
      show = false;
    }

    ActionBarStore.set({
      can_delete: false,
      undo: undo,
      show: show,
      save: () => updateTask(task),
    });
  }
</script>

{#if task}
  <div class="mb-4">
    <div>
      <div class="xl:border-b xl:pb-6">
        <div class="mt-2">
          {#if isEditingTitle}
            <FloatingInput
              bind:value={task.title}
              label="Title"
              placeholder="Enter task title"
              class="mt-2 w-full"
            />
          {:else}
            <div class="flex items-start">
              <h1
                class="text-2xl font-fredoka-one-regular"
                style="color:#220055;"
              >
                {task.title}
              </h1>
              {#if taskCreator}
                <button on:click={() => (isEditingTitle = true)}>
                  <svg
                    class="h-4 w-4 ml-2 text-gray-400 cursor-pointer"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                  >
                    <path d="M12 20h9"></path>
                    <path
                      d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"
                    ></path>
                  </svg>
                </button>
              {/if}
            </div>
          {/if}
          <p class="mt-2 text-sm text-gray-500">
            #{task.id} Created by
            <span class="font-medium text-gray-900">{task.creator?.name}</span>
            on {task.created}
          </p>
        </div>
      </div>
      <aside class="my-4">
        <h2 class="sr-only">Details</h2>
        <div class="flex flex-col sm:flex-row sm:justify-between py-1">
          <div class="flex items-center space-x-2">
            <svg
              class="h-5 w-5 text-green-500"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M14.5 1A4.5 4.5 0 0010 5.5V9H3a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-1.5V5.5a3 3 0 116 0v2.75a.75.75 0 001.5 0V5.5A4.5 4.5 0 0014.5 1z"
                clip-rule="evenodd"
              />
            </svg>
            {#if taskCreator || taskAssignee}
              <FloatingSelect
                label="Status"
                bind:value={task.status}
                options={userStore.id != task.user_id
                  ? status_options.filter((option) => option.is_user)
                  : status_options}
              />
            {:else}
              <div class="flex flex-col">
                <div class="text-sm font-medium text-gray-900">Status:</div>
                {task.status ? formatPrettyName(task.status) : "Pending"}
              </div>
            {/if}
          </div>
          <!-- <div class="flex items-center space-x-2 ">
					<svg
						class="h-5 w-5 text-gray-400"
						viewBox="0 0 20 20"
						fill="currentColor"
						aria-hidden="true"
					>
						<path
						fill-rule="evenodd"
						d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902.848.137 1.705.248 2.57.331v3.443a.75.75 0 001.28.53l3.58-3.579a.78.78 0 01.527-.224 41.202 41.202 0 005.183-.5c1.437-.232 2.43-1.49 2.43-2.903V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0010 2zm0 7a1 1 0 100-2 1 1 0 000 2zM8 8a1 1 0 11-2 0 1 1 0 012 0zm5 1a1 1 0 100-2 1 1 0 000 2z"
						clip-rule="evenodd"
						/>
					</svg>
					<span class="text-sm font-medium text-gray-900">4 comments</span>
				</div> -->
          <div class="flex items-center gap-4">
            <!-- <FloatingSelect label="Status" bind:value={task.priority} options={priority_options} /> -->
            {#if taskCreator}
              <StaffSelector
                label="Assignee"
                bind:staff_id={task.assigned_to}
              />
              <FloatingDateTime label="Due Date" bind:value={task.due_date} />
            {:else}
              <div class="flex flex-col">
                <div class="text-sm font-medium text-gray-900">Assignee:</div>
                {task.assigned_to ? task.assignee.name : "Unassigned"}
              </div>
              <div class="flex flex-col">
                <div class="text-sm font-medium text-gray-900">Due Date:</div>
                {formatDateTime(task.due_date) ?? "Not set"}
              </div>
            {/if}
          </div>
        </div>
      </aside>
      <div class="">
        <h2 class="sr-only">Description</h2>
        <div class="max-w-none group">
          <!-- {#if isEditingDescription}
            <RTE bind:content={task.description} />
          {:else}
            <RTEView bind:content={task.description} /> -->

          {#if taskCreator}
            <RTE bind:content={task.description} />
          {:else}
            <RTEView bind:content={task.description} />
          {/if}
          <!-- {/if} -->
        </div>
      </div>
    </div>
    {#if taskCreator && task.archived == 0}
      <button
        type="button"
        class="block rounded-md mt-4 bg-red-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
        on:click={() => archiveTask()}
      >
        Archive
      </button>
    {/if}
    <!-- <section
		aria-labelledby="activity-title"
		class="mt-8 xl:mt-10">
		<div class="divide-y divide-gray-200">
			<div class="pb-4">
				<h2
					id="activity-title"
					class="text-lg font-medium text-gray-900"
				>
					Activity
				</h2>
			</div>

			<div class="pt-6">
				<div class="flow-root">
					<ul role="list" class="-mb-8">
						<Comment />
						<Assignment />
						<Time />
					</ul>
				</div>

				<div class="mt-6">
					<AddComment bind:task_id={task_id} />
				</div>
			</div>
		</div>
	</section> -->
  </div>
{/if}
