<script>
  import { onMount } from "svelte";
  import { push } from "svelte-spa-router";
  import Role from "@shared/Role.svelte";
  import { jspa } from "@shared/jspa.js";
  import { StafferStore, RolesStore } from "@shared/stores.js";
  import { haveCommonElements } from "@shared/utilities.js";
  import Filter from "@shared/PhippsyTech/svelte-ui/Filter.svelte";
  import { jwt } from "@shared/stores.js";
  import ParticipantCard from "../ParticipantCard.svelte";
  import { slide } from "svelte/transition";
  import { flip } from "svelte/animate";
  import MiniJSON2CSV from "@shared/MiniJSON2CSV.svelte";

  export let search = "";

  let clients = [];

  let clientList = [];
  let action = "listStaffClientsByStaffId";
  let endpoint = "/Client/Staff";
  let data = { staff_id: StafferStore.id };

  let filters = [
    { label: "archived", enabled: false },
    { label: "on hold", enabled: false },
  ];

  $: stafferStore = $StafferStore;
  $: rolesStore = $RolesStore;

  let participantMailingList = [];

  jspa("/Client", "getClientMailingList", {}).then((result) => {
    participantMailingList = result.result;
  });

  let staffer_store_loaded = false;

  $: {
    if ($StafferStore !== null && !staffer_store_loaded) {
      getClients();
      staffer_store_loaded = true;
    }
  }

  function getClients() {
    data = { staff_id: stafferStore.id };
    if (
      haveCommonElements(rolesStore, ["admin", "therapist.lead", "accounts"])
    ) {
      action = "listClients";
      endpoint = "/Client";
      data = {};
    }

    // auditor list
    if (
      haveCommonElements(rolesStore, ["auditor"])
    ) {
      action = "listAllowedByUserId";
      endpoint = "/User/Participant";
      data = {};
    }


    jspa(endpoint, action, data).then((result) => {
      clients = result.result;

      clients.sort(function (a, b) {
        const nameA = a.client_name.toUpperCase(); // ignore upper and lowercase
        const nameB = b.client_name.toUpperCase(); // ignore upper and lowercase
        if (nameA < nameB) return -1;
        if (nameA > nameB) return 1;
        return 0; // names must be equal
      });

      clientList = clients; // Initialize clientList for filtering
    });
  }

  $: {
    // Filter by NDIS number or client name first
    if (is9DigitNumber(search)) {
      clientList = clients.filter(
        (client) =>
          client.ndis_number &&
          client.ndis_number.replace(/\s+/g, "") == search.replace(/\s+/g, "")
      );
    } else {
      clientList = clients.filter(
        (client) =>
          client.client_name &&
          client.client_name.toLowerCase().includes(search.toLowerCase())
      );
    }

    // Apply the "on hold" and "archived" filters
    const showOnHold = filters.find((f) => f.label === "on hold").enabled;
    const showArchived = filters.find((f) => f.label === "archived").enabled;

    clientList = clientList.filter((client) => {
      const isArchived = client.archived === "1";
      const isOnHold = client.on_hold;
      return (
        (showOnHold && showArchived && isArchived && isOnHold) ||
        (showOnHold && !showArchived && isOnHold && !isArchived) ||
        (!showOnHold && showArchived && isArchived && !isOnHold) ||
        (!showOnHold && !showArchived && !isArchived)
      );
    });

    // Sort the final clientList
    clientList.sort(function (a, b) {
      const nameA = a.client_name.toUpperCase(); // ignore upper and lowercase
      const nameB = b.client_name.toUpperCase(); // ignore upper and lowercase
      if (nameA < nameB) return -1;
      if (nameA > nameB) return 1;
      return 0; // names must be equal
    });
  }

  function is9DigitNumber(str) {
    // remove spaces
    str = str.replace(/\s+/g, "");
    // check if the string is 9 digits long and only contains numbers
    return /^\d{9}$/.test(str);
  }

  function handle(client_id) {
    push("/clients/" + client_id);
  }
</script>

<div
  class="bg-white px-3 mb-4 rounded-md pb-1 flex justify-between items-center"
>
  <Filter bind:filters />
  <Role roles={["admin"]}>
    <MiniJSON2CSV
      filename="participant-list.csv"
      bind:json_data={participantMailingList}
    />
  </Role>
</div>

<!-- <Role roles={["admin"]}>
    <label class="text-xs text-gray-400 px-6 flex justify-end">
        <input type="checkbox" bind:checked={showArchived} class="mr-2" />
        Include archived
    </label>
</Role> -->

<div
  class="grid grid-cols-1 gap-2 sm:gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4"
>
  {#each clientList as client, index (client.client_id)}
    <!--
       animate:flip={{ duration: 50 }}
            in:slide|global={{ duration: 150, delay: 1 * index }}
            -->
    <div>
      <ParticipantCard {client} />
    </div>
  {/each}
</div>
<!-- 
<ul role="list" class="divide-y divide-gray-100 bg-white">
    {#each clientList as client}
        <li
            class="relative flex justify-between py-2 px-5 text-gray-700 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
        >
            <div
                on:click={() => handle(client.client_id)}
                class="min-w-0 flex-auto"
            >
                <p
                    class="text-sm leading-6 {client.archived == 1
                        ? 'opacity-50'
                        : ''}"
                >
                    <span class="absolute inset-x-0 -top-px bottom-0"></span>
                    {client.client_name}
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
    {/each}
</ul> -->
