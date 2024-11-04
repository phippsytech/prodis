<script>
  import Container from "@shared/Container.svelte";
  import { PencilIcon } from "heroicons-svelte/24/outline";
  import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
  import { onMount, onDestroy } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { getStaffer } from "@shared/api.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import {
    formatDate,
    convertMinutesToHoursAndMinutes,
  } from "@shared/utilities.js";

  export let staff_id;
  export let billed = false;

  const date = new Date();
  const start_year = date.getFullYear() - 1;
  const end_year = date.getFullYear() + 1;

  let staffer = {};
  let timetracking = [];
  let timetrackingList = [];
  let service_codes = [];
  let selected_service_codes = [];
  // let filter = {
  //     start_date: "2023-06-19",
  //     end_date: "2023-06-25",
  // };

  let claim_types = [
    { option: "Direct Service (In-Person with Participant)", value: "" },
    { option: "Cancellation", value: "CANC" },
    { option: "NDIA Required Report", value: "REPW" },
    { option: "Provider Travel", value: "TRAN" },
    {
      option: "Non-Face-to-Face Services (Did not see the participant)",
      value: "NF2F",
    },
  ];

  function getOption(value) {
    if (value === null) value = "";
    let option = "";
    claim_types.forEach((item) => {
      if (item.value == value) {
        option = item.option;
      }
    });
    if (value == null && billed == false) {
      option = "<span class='text-red-400'>NOT SET</span>";
    }
    return option;
  }

  onMount(async () => {
    staffer = await getStaffer(staff_id);

    BreadcrumbStore.set({
      path: [
        { url: "/staff", name: "Staff" },
        { url: null, name: staffer.name },
      ],
    });

    jspa("/TimeTracking", "listTimeTrackingByStaffId", {
      staff_id: staff_id,
      billed: billed,
    })
      .then((result) => {
        timetracking = result.result;

        // Make a distinct list of service codes to use as a filter.
        service_codes = [
          ...new Set(timetracking.map((item) => item.service_code)),
        ];

        filterTimeTrackings();
      })
      .catch((error) => (timetracking = []));
  });

  function toggleServiceCode(service_code) {
    if (selected_service_codes.includes(service_code)) {
      selected_service_codes = selected_service_codes.filter(
        (code) => code !== service_code
      );
    } else {
      selected_service_codes = [...selected_service_codes, service_code];
    }
    filterTimeTrackings();
  }

  function filterTimeTrackings() {
    timetrackingList = JSON.parse(JSON.stringify(timetracking));
    return;

    // Filter by date
    if (filter.start_date && filter.end_date) {
      timetrackingList = timetrackingList.filter((item) => {
        let item_date = new Date(item.session_date);
        let start_date = new Date(filter.start_date);
        let end_date = new Date(filter.end_date);
        return item_date >= start_date && item_date <= end_date;
      });
    }

    // Filter by service code
    if (selected_service_codes.length > 0) {
      timetrackingList = timetrackingList.filter((item) =>
        selected_service_codes.includes(item.service_code)
      );
    }
  }
</script>

<div class="flex justify-between mb-4">
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Billables for {staffer.name}
  </div>
  <button
    on:click={() => push("/staff/" + staffer.id + "/billables/add")}
    type="button"
    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
    >Add Billable</button
  >
</div>

<table class="min-w-full">
  <thead>
    <tr class="border-b">
      <th class="text-left text-xs px-2">Date</th>
      <th class="text-left text-xs px-2">Client</th>
      <th class="text-left text-xs px-2">Support Item</th>
      <th class="text-xs px-2">Claim Type</th>
      <th class="text-xs px-2">Quantity</th>

      <th></th>
    </tr>
  </thead>
  {#each timetracking as session, index}
    <tr class="border-b hover:bg-gray-50">
      <td><span class="">{formatDate(session.session_date)}</span></td>
      <td
        ><a
          class="font-bold text-blue-600 underline"
          href="/#/clients/{session.client_id}">{session.client_name}</a
        ></td
      >
      <td><span class="">{session.service_code}</span></td>
      {#if session.session_duration == null || session.session_duration <= 0}
        <td colspan="2" class="text-center text-xs"> Note only</td>
      {:else}
        <td class="text-center text-xs">
          {@html getOption(session.claim_type)}
        </td>
        <td class="text-center"
          >{#if session.session_duration}
            {#if session.unit_type == "km"}{session.session_duration}<span
                class="text-xs">kms</span
              >{:else}{@html convertMinutesToHoursAndMinutes(
                session.session_duration
              )}{/if}{:else}...{/if}
          {#if session.actual_travel_time > 0}<span class="text-sm"
              ><br />+ {@html convertMinutesToHoursAndMinutes(
                session.actual_travel_time
              )} travel
            </span>{/if}
        </td>
      {/if}
      <td>
        {#if session.claim_type == "TRAN"}
          <a
            class="inline-flex justify-center rounded-md bg-blue-600 px-1 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
            href="/#/trips/{session.trip_id}">Edit</a
          >
        {:else}
          <a
            class="inline-flex justify-center rounded-md bg-blue-600 px-1 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
            href="/#/billables/{session.id}">Edit</a
          >
        {/if}
      </td>
    </tr>
  {:else}
    <tr class="border-b">
      <td colspan="5" class="text-center p-4">No billable items found</td>
    </tr>
  {/each}
</table>
