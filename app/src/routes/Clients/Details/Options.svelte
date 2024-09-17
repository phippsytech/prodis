<script>
	import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
	import { jspa } from "@shared/jspa.js";
	import { toastSuccess, toastError } from "@shared/toastHelper.js";
	import { ModalStore } from "@app/Overlays/stores";
  import SuspendForm from "./SuspendForm.svelte";

	export let client = {};
	export let stored_client = {};

	let show = false;

	function toggle() {
		show = !show;
	}

	function disableRestrictivePractices() {
		jspa("/Client", "disableRestrictivePractices", { id: client.id }).then(
		(result) => {
			client.restrictive_practices = false;
			// Make a copy of the object
			stored_client = Object.assign({}, client);
			show = false;
			toastSuccess("Restrictive practices have been disabled.");
		},
		);
	}

	function enableRestrictivePractices() {
		jspa("/Client", "enableRestrictivePractices", { id: client.id }).then(
		(result) => {
			client.restrictive_practices = true;
			// Make a copy of the object
			stored_client = Object.assign({}, client);
			show = false;
			toastSuccess("Restrictive practices have been enabled.");
		},
		);
	}

	function archiveClient() {
		jspa("/Client", "archiveClient", { id: client.id }).then((result) => {
		client.archived = true;
		// Make a copy of the object
		stored_client = Object.assign({}, client);
		show = false;
		toastSuccess("Client has been archived.");
		});
	}

	function restoreClient() {
		jspa("/Client", "restoreClient", { id: client.id }).then((result) => {
		client.archived = false;
		// Make a copy of the object
		stored_client = Object.assign({}, client);
		show = false;
		toastSuccess("Client has been restored.");
		});
	}

	function suspendClient(data) {
    jspa("/Client", "suspendClient", { id: client.id, reason: data.reason }).then((result) => {
      client.on_hold = true;
      stored_client = Object.assign({}, client);
      show = false;
      toastSuccess("Client has been put on hold.");
		});
	}

	function resumeClient(data) {
		jspa("/Client", "resumeClient", { id: client.id, reason: data.reason }).then((result) => {
		client.on_hold = false;
		stored_client = Object.assign({}, client);
		show = false;
		toastSuccess("Client has been taken off hold.");
		});
	}

	function confirmationModal(action) {
    let reason = { reason: "" };
		if (action == "suspend") {
			ModalStore.set({
				label: "Provide a reason suspending the client",
				show: true,
				props: reason,
				component: SuspendForm,
				action_label: "Update",
				action: () => suspendClient(reason),
			});
		}

		if (action == "resume") {
			ModalStore.set({
				label: "Provide a reason resuming the client",
				show: true,
				props: reason,
				component: SuspendForm,
				action_label: "Update",
				action: () => resumeClient(reason),
			});
		}
			
	}
</script>

<div class="relative inline-block text-left">
  <div>
    <button
      on:click={() => toggle()}
      type="button"
      class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-indigo-600 hover:text-white"
      id="menu-button"
      aria-expanded="true"
      aria-haspopup="true"
    >
      Options
      <svg
        class="-mr-1 h-5 w-5 text-gray-400"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
          clip-rule="evenodd"
        />
      </svg>
    </button>
  </div>

  {#if show}
    <!--
      Dropdown menu, show/hide based on menu state.
  
      Entering: "transition ease-out duration-100"
        From: "transform opacity-0 scale-95"
        To: "transform opacity-100 scale-100"
      Leaving: "transition ease-in duration-75"
        From: "transform opacity-100 scale-100"
        To: "transform opacity-0 scale-95"
    -->
    <div
      class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
      tabindex="-1"
    >
      <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-700" -->
      <!-- <div class="py-1" role="none">
        <a href="/#/" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-0">Edit</a>
        <a href="/#/" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-1">Duplicate</a>
      </div> -->

      <div class="py-1" role="none">
        {#if client.restrictive_practices == true}
          <span
            on:click={() => disableRestrictivePractices()}
            class="cursor-pointer text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-600 hover:text-white"
            role="menuitem"
            tabindex="-1"
            id="menu-item-2"
            title="Take client off hold">Disable restrictive practices</span
          >
        {:else}
          <span
            on:click={() => enableRestrictivePractices()}
            class="cursor-pointer text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-600 hover:text-white"
            role="menuitem"
            tabindex="-1"
            id="menu-item-2"
            title="Take client off hold">Enable restrictive practices</span
          >
        {/if}
      </div>

      <div class="py-1" role="none">
        {#if client.on_hold == 1}
          <span
            on:click={() => confirmationModal('resume')}
            class="cursor-pointer text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-600 hover:text-white"
            role="menuitem"
            tabindex="-1"
            id="menu-item-2"
            title="Take client off hold">Take Off Hold</span
          >
        {:else}
          <span
            on:click={() => confirmationModal('suspend')}
            class="cursor-pointer text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-600 hover:text-white"
            role="menuitem"
            tabindex="-1"
            id="menu-item-2"
            title="Place client on hold">Put On Hold</span
          >
        {/if}
      </div>
      <!-- <div class="py-1" role="none">
        <a href="/#/" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-4">Share</a>
        <a href="/#/" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-5">Add to favorites</a>
      </div> -->
      <div class="py-1" role="none">
        {#if client.archived == 1}
          <span
            on:click={() => restoreClient()}
            class="cursor-pointer text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-600 hover:text-white"
            role="menuitem"
            tabindex="-1"
            id="menu-item-6"
            title="Restore Client">Restore</span
          >
        {:else}
          <span
            on:click={() => archiveClient()}
            class="cursor-pointer text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-600 hover:text-white"
            role="menuitem"
            tabindex="-1"
            id="menu-item-6"
            title="Archive Client">Archive</span
          >
        {/if}
      </div>
    </div>
  {/if}
</div>
