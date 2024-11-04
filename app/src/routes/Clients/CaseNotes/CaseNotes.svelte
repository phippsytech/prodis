<script>
  import { push } from "svelte-spa-router";
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { getClient } from "@shared/api.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { SpinnerStore } from "@app/Overlays/stores.js";
  import { formatDate, convertToLocalDate } from "@shared/utilities.js";
  import Role from "@shared/Role.svelte";

  export let params;

  let client_id = params.client_id;
  let casenotes = [];
  let client = {};

  SpinnerStore.set({ show: true, message: "Loading Case Notes" });

  onMount(async () => {
    client = await getClient(client_id);

    document.title = client.name + " - Case Notes";
    BreadcrumbStore.set({
      path: [{ url: "/clients", name: "Clients" }, { name: client.name }],
    });

    await jspa("/Client/CaseNote", "listCaseNotesByClientId", {
      client_id: client.id,
    })
      .then((result) => {
        casenotes = result.result;
        casenotes = casenotes.filter((casenote) => casenote.notes !== null);
      })
      .finally(() => {
        SpinnerStore.set({ show: false });
      });
  });

  function handle(id) {
    push("/clients/" + client.id + "/casenotes/" + id);
  }
</script>

<div class="bg-white px-4 py-4">
  <div class="flex justify-between mb-2">
    <div
      class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
      Case Notes for {client.name}
    </div>
    <Role roles={["therapist", "admin"]}>
      <button
        on:click={() => push("/clients/" + client.id + "/casenotes/add")}
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add Case Note</button
      >
    </Role>
  </div>
</div>

<ul role="list" class="divide-y divide-gray-100">
  {#each casenotes as casenote}
    <li
      class="relative flex justify-between py-2 px-5 text-gray-700 hover:text-indigo-600 hover:bg-gray-50"
    >
      <div on:click={() => handle(casenote.id)} class="min-w-0 flex-auto">
        <p class="text-sm font-semibold leading-6">
          <span class="absolute inset-x-0 -top-px bottom-0"></span>
          {convertToLocalDate(casenote.created)}
          : <span class="text-sm">{casenote.staff_name}</span>
          {#if casenote.session_duration}<span class="text-xs font-light italic"
              >- {casenote.session_duration} mins</span
            >{/if}
        </p>
        <p class=" text-xs text-gray-500 truncate">
          {@html casenote.notes}
        </p>
      </div>
      <div class="flex items-center justify-end gap-x-4 flex-none">
        <svg
          class="h-5 w-5 flex-none text-gray-400"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
    </li>
  {:else}
    <li
      class="relative flex justify-between py-2 px-5 text-gray-700 hover:text-indigo-600 hover:bg-gray-50"
    >
      No case notes found for this client.
    </li>
  {/each}
</ul>
