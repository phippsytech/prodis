<script>
  import { push } from "svelte-spa-router";
  import { PlusIcon, FunnelIcon } from "heroicons-svelte/24/outline";
  import { slide } from "svelte/transition";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { formatPrettyName, formatDate } from "@shared/utilities.js";
  import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import Pager from "@shared/PhippsyTech/svelte-ui/Pager.svelte";

  import Filter from "@shared/PhippsyTech/svelte-ui/Filter.svelte";
  import Role from "@shared/Role.svelte";
  import MiniJSON2CSV from "@shared/MiniJSON2CSV.svelte";

  import TrainingFilter from "./TrainingFilter.svelte";

  import { ModalStore, SlideOverStore } from "@app/Overlays/stores.js";

  $: slideoverStore = $SlideOverStore;

  let filters = [];

  let trainings = [];
  let stored_trainings = [];

  let staffs;
  let staff_id;
  let start_date;
  let end_date;

  let breadcrumbs_path = [];
  let breadcrumbs_action = {
    icon: PlusIcon,
    event: () => push("/registers/trainings/add"),
  };

  jspa("/Register/Training", "listTrainings", {})
    .then((result) => {
      trainings = result.result;
      stored_trainings = [...trainings];
      // sort the trainings reverse chronologically
      trainings.sort(function (a, b) {
        let aDateTime = Date.parse(a.date);
        let bDateTime = Date.parse(b.date);
        return bDateTime - aDateTime;
      });
    })
    .catch((error) => {});

  BreadcrumbStore.set({
    path: [{ url: "/registers", name: "Registers" }],
  });

  let filter = {};

  function applyFilter(filter) {
    if (filter.start_date) {
      trainings = trainings.filter(
        (training) => Date.parse(training.date) >= Date.parse(filter.start_date)
      );
    }
    if (filter.end_date) {
      trainings = trainings.filter(
        (training) =>
          Date.parse(training.completion_date) >= Date.parse(filter.end_date)
      );
      console.log(trainings);
    }
  }
  function clearFilter(filter) {
    filter = {};
    trainings = [...stored_trainings];
  }

  function showFilter() {
    SlideOverStore.set({
      label: "Filter",
      show: true,
      props: filter,
      component: TrainingFilter,
      action_label: "Apply",
      delete_label: "Clear",
      action: () => applyFilter(filter),
      delete: () => clearFilter(filter),
    });
  }
</script>

<div>
  <div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
      <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
      >
        Training Register
      </h1>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
      <button
        on:click={() => push("/registers/trainings/add")}
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add Training</button
      >
    </div>
  </div>

  <nav
    class="bg-white text-slate-300 rounded-lg flex justify-between space-x-2 items-center px-2 py-1 mt-4 mx-2"
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
        filename="training-register.csv"
        bind:json_data={trainings}
      />

      <!-- <button
        on:click={() => execCommand("outdent")}
        class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
      >
        <span class="sr-only">Outdent</span>
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
            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
          ></path>
        </svg>
      </button> -->
    </div>
  </nav>

  <div class="mt-4 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <div
          class="overflow-hidden shadow ring-1 ring-indigo-100 sm:rounded-lg"
        >
          <table class="min-w-full divide-y divide-indigo-50">
            <thead class="bg-slate-50/50">
              <tr>
                <th
                  scope="col"
                  class="py-2 px-4 text-left text-xs font-medium text-slate-500"
                  >Training</th
                >
                <th
                  scope="col"
                  class="px-4 py-2 text-left text-xs font-medium text-slate-500"
                  >Trainer</th
                >

                <th
                  scope="col"
                  class="px-4 py-2 text-left text-xs font-medium text-slate-500"
                  >Date</th
                >
                <th
                  scope="col"
                  class="px-4 py-2 text-left text-xs font-medium text-slate-500"
                  >Completed</th
                >
                <th scope="col" class="relative py-2 pl-3 pr-4 sm:pr-6">
                  <span class="sr-only">Edit</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-indigo-100 bg-white">
              {#each trainings as training, index (training.id)}
                <tr class="hover:bg-indigo-50">
                  <td
                    class="whitespace-nowrap py-3 px-4 text-sm font-medium text-gray-900"
                    >{training.course_title}</td
                  >
                  <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                    >{training.trainer}</td
                  >

                  <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                    >{training.date ? formatDate(training.date) : "N/A"}</td
                  >
                  <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                    >{training.completion_date
                      ? formatDate(training.completion_date)
                      : "N/A"}</td
                  >
                  <td
                    class="relative whitespace-nowrap py-3 px-4 text-right text-sm font-medium"
                  >
                    <a
                      href="/#/registers/trainings/{training.id}"
                      class="text-indigo-600 hover:text-indigo-900">View</a
                    >
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- <Pager totalResults="130" /> -->
