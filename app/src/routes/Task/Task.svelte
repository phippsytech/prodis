<script>
	import Container from "@shared/Container.svelte";
	import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
	import FloatingDateTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateTime.svelte";
	import StaffSelector from "@app/routes/Billables/StaffSelector.svelte";
	import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
	import RTE from "@shared/RTE/RTE.svelte";
	import { BreadcrumbStore, UserStore } from "@shared/stores.js";
	import { jspa } from "@shared/jspa.js";
	import { toastSuccess, toastError } from "@shared/toastHelper.js";
	import { formatDateTime } from "@shared/utilities.js";
	import AddComment from "./AddComment.svelte";
	import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

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

	let task_id = params.task_id;
	

	let isEditingDescription = false;
	let isEditingTitle = false;
	let stored_task = {};

	getTask(task_id);

	let task = null;

	let status_options = [
        { option: "Pending", value: "pending" },
        { option: "In Progress", value: "in_progress" },
        { option: "Completed", value: "completed" }
    ];

	let status_options_user = [
        { option: "Pending", value: "pending" },
        { option: "In Progress", value: "in_progress" }
    ];

    let priority_options = [
        { option: "Low", value: "low" },
        { option: "Normal", value: "normal" },
        { option: "High", value: "high" },
        { option: "Urgent", value: "urgent" }
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
			if (task.status === 'in_progress' && stored_task.status != task.status) {
				const now = new Date().toISOString().slice(0, 19).replace("T", " ");

				task.in_progress_at = now;
				task.in_progress_by = userStore.id;
			}

			if (stored_task.assigned_to != task.assigned_to) {
				task.assigned_by = userStore.id;
			}
			
			// format due date when updating
			if (task.due_date) {
				task.due_date = task.due_date ? new Date(task.due_date).toISOString().slice(0, 19).replace("T", " ") : '';
			}

			jspa("/Task", "updateTask", task)
				.then((result) => {
					//task = result.result;

					// toast messages
					if (isEditingTitle) {
						toastSuccess("Title updated successfully");
					} else if (isEditingDescription) {
						toastSuccess("Description updated successfully");
					} else {
						toastSuccess("Status updated successfully");
					}

					isEditingDescription = false;
					isEditingTitle = false;
					
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

	let show = false;
    $: {
        if (task?.title != stored_task?.title || task?.description != stored_task?.description || task?.status != stored_task?.status || task?.due_date != stored_task?.due_date || task?.assigned_to != stored_task?.assigned_to || task?.priority != stored_task?.priority) {
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
		<div class="md:flex md:items-center md:justify-between md:space-x-4 xl:border-b xl:pb-6">
			<div class="mt-2">
				{#if isEditingTitle}
					<FloatingInput
						bind:value={task.title}
						label="Title"
						placeholder="Enter task title"
						class="mt-2" />
				{:else}
					<div class="flex">
						<h1 class="text-2xl font-fredoka-one-regular" style="color:#220055;">
							{task.title}
						</h1>
						{#if userStore.id == task.user_id}
						<button
								on:click={() => isEditingTitle = true}
							>
							<svg 
								class="h-4 w-4 ml-2 text-gray-400 cursor-pointer" 
								viewBox="0 0 24 24" 
								fill="none" 
								stroke="currentColor" 
								stroke-width="2" 
								stroke-linecap="round" 
								stroke-linejoin="round"
								aria-hidden="true">
								<path d="M12 20h9"></path>
								<path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
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
		<div class="flex flex-col space-y-1 sm:flex-row sm:justify-between border-b border-gray-200 py-2">
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
				<FloatingSelect label="Status" bind:value={task.status} options={userStore.id != task.user_id ? status_options_user : status_options} />
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
				{#if userStore.id == task.user_id}
					<StaffSelector label="Assignee" bind:staff_id={task.assigned_to} />
				{:else}
					<div class="flex flex-col">
						<div class="text-sm font-medium text-gray-900">
							Assignee: 
					</div>
						{task.assigned_to ?? 'Unassigned'}
					</div>
				{/if}
				<FloatingDateTime label="Due Date" bind:value={task.due_date}  readOnly="{userStore.id == task.user_id ? false : true}"/>
			</div>
		</div>
		</aside>
		<div class="py-1">
			<h2 class="sr-only">Description</h2>
			<div class="max-w-none group">
				{#if isEditingDescription}
					<RTE bind:content={task.description} />
				{:else}
					{@html task.description}
					{#if userStore.id == task.user_id}
					<button
						class="ml-2"
						on:click={() => isEditingDescription = true}
					>
						<svg 
							class="h-4 w-4 text-gray-400 cursor-pointer" 
							viewBox="0 0 24 24" 
							fill="none" 
							stroke="currentColor" 
							stroke-width="2" 
							stroke-linecap="round" 
							stroke-linejoin="round"
							aria-hidden="true">
							<path d="M12 20h9"></path>
							<path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
						</svg>
					</button>
					{/if}
				{/if}
			</div>
		</div>
	</div>
	<section
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
	</section>
</div>
{/if}
