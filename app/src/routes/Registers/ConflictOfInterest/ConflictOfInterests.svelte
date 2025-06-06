<script>
  import { push } from "svelte-spa-router";
  import { PlusIcon } from "heroicons-svelte/24/outline";
  import { slide } from "svelte/transition";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { formatDate, formatPrettyName } from "@shared/utilities.js";
  import { ModalStore, SlideOverStore } from "@app/Overlays/stores.js";
  import MiniJSON2CSV from "@shared/MiniJSON2CSV.svelte";
  // import Pager from "@shared/PhippsyTech/svelte-ui/Pager.svelte";
  import Filter from "@shared/PhippsyTech/svelte-ui/Filter.svelte";
  import ConflictOfInterestFilter from "./ConflictOfInterestFilter.svelte";
  $: slideoverStore = $SlideOverStore;

  let conflictofinterests = [];
  let storedOfConflictOfInterests = [];
  let show_filter = false;
  let filters = [];

  let totalItems = 0;

  let filter = {};

  jspa("/Register/ConflictOfInterest", "listConflictOfInterest", {}).then(
    (result) => {
      conflictofinterests = result.result;

      totalItems = conflictofinterests.length;

      storedOfConflictOfInterests = conflictofinterests;

      // sort the conflictofinterests reverse chronologically
      conflictofinterests.sort(function (a, b) {
        let aDateTime = Date.parse(a.date_identified);
        let bDateTime = Date.parse(b.date_identified);
        return bDateTime - aDateTime;
      });
    }
  );

  BreadcrumbStore.set({
    path: [{ url: "/registers", name: "Registers" }],
  });

  function applyFilter(filter) {
    if (filter.date_identified) {
      conflictofinterests = conflictofinterests.filter(
        (conflict) =>
          Date.parse(conflict.date_identified) >=
          Date.parse(filter.date_identified)
      );
    }
    if (filter.date_resolved) {
      conflictofinterests = conflictofinterests.filter(
        (conflict) =>
          Date.parse(conflict.date_identified) >=
          Date.parse(filter.date_identified)
      );
    }
    if (filter.status) {
      conflictofinterests = conflictofinterests.filter(
        (conflict) => conflict.status == filter.status
      );
    }

    if (filter.staff_id) {
      conflictofinterests = conflictofinterests.filter(
        (conflict) => conflict.staff_id == filter.staff_id
      );
    }

    if (filter.type) {
      conflictofinterests = conflictofinterests.filter(
        (conflict) => conflict.type == filter.type
      );
    }
  }
  function clearFilter(filter) {
    filter = {};
    conflictofinterests = storedOfConflictOfInterests;
  }

  function showFilter() {
    SlideOverStore.set({
      label: "Filter",
      show: true,
      props: filter,
      component: ConflictOfInterestFilter,
      action_label: "Apply",
      delete_label: "Clear",
      action: () => applyFilter(filter),
      delete: () => clearFilter(filter),
    });
  }
</script>

<div class="sm:flex sm:items-center mb-4">
  <div class="sm:flex-auto">
    <h1
      class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
      Conflict of Interest Register
    </h1>
  </div>
  <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
    <button
      on:click={() => push("/registers/conflictofinterests/add")}
      type="button"
      class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
      >Add Conflict Of Interest</button
    >
  </div>
</div>
<nav
  class="bg-white text-slate-300 rounded-lg flex justify-between space-x-2 items-center px-2 py-1 mt-4 mx-2 mb-2"
  aria-label="Toolbar"
>
  <div class="flex space-x-2 items-center">
    <button
      on:click={() => showFilter()}
      class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
    >
      <span class="sr-only">Filter</span>

      <svg
        data-slot="icon"
        height="1em"
        fill="none"
        stroke-width="1.5"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z"
        ></path>
      </svg>
    </button>
  </div>
  <div class="flex space-x-2 items-center">
    <MiniJSON2CSV
      filename="conflict-of-interest-register.csv"
      bind:json_data={conflictofinterests}
    />
  </div>
</nav>
<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8 mt-2">
  <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
    <div
      class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg"
    >
      <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="py-2 px-4 text-left text-xs font-medium text-slate-500"
            >
              Conflicts
            </th>
            <th
              scope="col"
              class="py-2 px-4 text-left text-xs font-medium text-slate-500"
            >
              Parties Involved
            </th>
            <th
              scope="col"
              class="py-2 px-4 text-left text-xs font-medium text-slate-500"
            >
              Reporter
            </th>
            <th
              scope="col"
              class="py-2 px-4 text-left text-xs font-medium text-slate-500"
            >
              Conflict Date
            </th>
            <th
              scope="col"
              class="py-2 px-4 text-left text-xs font-medium text-slate-500"
            >
              Resolution Date
            </th>
            <th
              scope="col"
              class="py-2 px-4 text-left text-xs font-medium text-slate-500"
            >
              Status
            </th>
            <th
              scope="col"
              class="py-2 px-4 text-left text-xs font-medium text-slate-500"
            >
              Type
            </th>
            <th scope="col" class="relative py-3.5 pl-3 pr-4 xs:pr-6">
              <span class="sr-only">View</span>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          {#each conflictofinterests as conflict}
            <tr>
              <td
                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"
              >
                {@html conflict.description.length > 50
                  ? conflict.description.slice(0, 50) + "..."
                  : conflict.description}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {conflict.parties_involved ? conflict.parties_involved : "N/A"}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {conflict.reported_by ? conflict.reported_by : "N/A"}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {formatDate(conflict.date_identified)}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {formatDate(conflict.date_resolved)
                  ? formatDate(conflict.date_resolved)
                  : "N/A"}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {formatPrettyName(conflict.status)}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {conflict.type ? formatPrettyName(conflict.type) : "N/A"}
              </td>
              <td
                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6"
              >
                <a
                  href="/#/registers/conflictofinterests/{conflict.id}"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  View
                </a>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- <Pager totalResults={totalItems} /> -->
