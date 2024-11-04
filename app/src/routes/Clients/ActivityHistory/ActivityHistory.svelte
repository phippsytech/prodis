<script>
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { slide } from "svelte/transition";
  import { getClient } from "@shared/api.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { formatDateTime } from "@shared/utilities.js";

  export let params;

  let client_id = params.client_id;
  let activities = [];
  // let invoice_summary = [];
  let client = {};

  onMount(async () => {
    client = await getClient(client_id);
    BreadcrumbStore.set({
      path: [
        { url: "/clients", name: "Clients" },
        { url: "/clients/" + client_id, name: client.name },
      ],
    });
  });

  jspa("/ActivityLog", "listActivityLogs", {
    entity_type: "participant",
    entity_id: client_id,
  }).then((result) => {
    activities = result.result;
  });
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Activity History
</div>

<!-- Header -->
<div class="hidden sm:block">
  <div
    class="grid grid-cols-12 border-b border-gray-200 items-center py-1 text-xs font-medium text-gray-500 gap-4"
  >
    <div class="grid grid-cols-3 gap-4 items-center col-span-10">
      <div>Action Type</div>
      <div>Reason</div>
      <div>User</div>
    </div>
    <div class="col-span-2">Date</div>
  </div>
</div>

<!-- Activities -->
{#if activities.length > 0}
  {#key activities}
    {#each activities as item, index}
      <div
        in:slide|global={{ duration: 150 }}
        class="grid grid-cols-12 border-b border-gray-200 items-center hover:bg-indigo-50 py-1 cursor-pointer gap-4"
      >
        <div
          class="grid grid-cols-1 gap-0 sm:gap-4 sm:grid-cols-3 w-full items-center col-span-8 sm:col-span-10"
        >
          <div class="text-sm">{item.action_type}</div>
          <div class="text-xs whitespace-no-wrap">
            {item.reason}
          </div>
          <div class="text-xs whitespace-no-wrap">
            {item.user_id == 0 ? "System Generated" : item.user_name}
          </div>
        </div>
        <div class="font-medium text-sm sm:text-base col-span-4 sm:col-span-2">
          {formatDateTime(item.timestamp)}
        </div>
      </div>
    {/each}
  {/key}
{:else}
  <div class="text-center text-gray-500 py-4">There are no records.</div>
{/if}
