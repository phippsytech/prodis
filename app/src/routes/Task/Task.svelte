<script>
	import Container from "@shared/Container.svelte";
	import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
	import FloatingTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte";
	import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
	import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
	import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
	import RTE from "@shared/RTE/RTE.svelte";
	import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
	import { jspa } from "@shared/jspa.js";
	import { toastSuccess, toastError } from "@shared/toastHelper.js";
	import { formatDate } from "@shared/utilities.js";
	import AddComment from "./AddComment.svelte";
	import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

	//   temporary static components
	import Assignment from "../Tickets/Activity/Assignment.svelte";
	import Comment from "../Tickets/Activity/Assignment.svelte";
	import Time from "../Tickets/Activity/Assignment.svelte";

	// Page metadata
	document.title = "View Task";
	BreadcrumbStore.set({
		path: [{ url: "/tasks", name: "Tasks" }, { name: "View Task" }],
	});


	export let params;

	let task_id = params.task_id;

	let isEditingDescription = false;
	let isEditingTitle = false;
	let stored_task = {};

	getTask(task_id);

	let task = null;

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
			jspa("/Task", "updateTask", task)
				.then((result) => {
					task = result.result;

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
					console.log(task);
					
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
        if (task?.title != stored_task?.title || task?.description != stored_task?.description || task?.status != stored_task?.status) {
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
					</div>
				{/if}
				<p class="mt-2 text-sm text-gray-500">
					#{task.id} Created by
					<a href="/#/" class="font-medium text-gray-900">{task.created_by}</a>
					on {formatDate(task.created_date)} at {task.created_time}
				</p>
			</div>
		</div>
		<aside class="my-4">
		<h2 class="sr-only">Details</h2>
		<div class="flex flex-col space-y-1 sm:flex-row sm:justify-between">
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
			<FloatingSelect label="Status" bind:value={task.status} options={status_options} />
			</div>
			<div class="flex items-center space-x-2">
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
			</div>
			<div class="flex items-center space-x-2">
				<svg
					class="h-5 w-5 text-gray-400"
					viewBox="0 0 20 20"
					fill="currentColor"
					aria-hidden="true"
				>
					<path
					fill-rule="evenodd"
					d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z"
					clip-rule="evenodd"
					/>
				</svg>
				<span class="text-sm font-medium text-gray-900"
					>Due {formatDate(task.due_date)} at {task.due_time}</span
				>
			</div>
		</div>
		<div class="mt-4 border-b border-t border-gray-200 py-2">
			<div>
				<!-- should be 1 person -->
				<h2 class="text-sm font-medium text-gray-500">Assignee</h2>
				<ul role="list" class="mt-1 space-y-1">
					<li class="flex justify-start">
						<a href="/#/" class="flex items-center space-x-3">
							<div class="text-sm font-medium text-gray-900">
							{task.assigned_to}
							</div>
						</a>
					</li>
				</ul>
			</div>
		</div>
		</aside>
		<div class="py-1">
			<h2 class="sr-only">Description</h2>
			<div class="max-w-none group">
				{#if isEditingDescription}
					<RTE height="250" bind:content={task.description} />
				{:else}
					{@html task.description}
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
