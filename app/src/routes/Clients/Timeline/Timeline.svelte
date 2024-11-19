<script>
  import { ClientStore, BreadcrumbStore, RolesStore } from "@shared/stores.js";
  import List from "./List.svelte";
  import { onMount, onDestroy } from "svelte";
  import { push } from "svelte-spa-router";
  import { getClient } from "@shared/api.js";
  import Role from "@shared/Role.svelte";
  import { haveCommonElements } from "@shared/utilities.js";

  export let params;

  $: rolesStore = $RolesStore;

  // export let client_id
  let client_id = params.client_id;
  let client;
  let ready = false;
  let show_filter = false;

  onMount(async () => {
    client = await getClient(client_id);
    BreadcrumbStore.set({
      path: [
        { name: "Clients", url: "/clients" },
        { name: client.name, url: "/clients/" + client.id },
      ],
    });
    ready = true;
  });

  function showFilter() {
    show_filter = true;
  }
</script>

{#if haveCommonElements( rolesStore, ["stakeholder", "timeline", "house.lead", "house"] )}
  {#if ready}
    <div class="flex items-center justify-between">
      <div
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
      >
        {#if show_filter}Filtered
        {/if}Timeline
      </div>

      <div class="flex gap-x-2 justify-end">
        {#if !show_filter}
          <button
            on:click={() => showFilter()}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Filter</button
          >
        {/if}

        <button
          on:click={() => push("/clients/" + client.id + "/timeline/export")}
          type="button"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >Export</button
        >
        <Role roles={["house", "house.lead"]}>
          <div
            on:click={() => push("/clients/" + client.id + "/forms")}
            class="flex items-center space-x-2 px-5 cursor-pointer"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-gray-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
          </div>
        </Role>
      </div>
    </div>

    <List bind:client_id={client.id} bind:show_filter />
  {/if}
{/if}
