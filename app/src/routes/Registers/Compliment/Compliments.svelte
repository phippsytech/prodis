<script>
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { SlideOverStore } from "@app/Overlays/stores.js";
    import { formatDate, formatPrettyName } from "@shared/utilities.js";
    import ComplimentFilter from "./ComplimentFilter.svelte";
    import MiniJSON2CSV from "@shared/MiniJSON2CSV.svelte";

    let compliments = [];
    let stored_compliments = [];

    jspa("/Register/Compliment", "listCompliments", {}).then((result) => {
        compliments = result.result;
        stored_compliments = [...compliments];
        // sort the compliments reverse chronologically
        compliments.sort(function (a, b) {
            let aDateTime = Date.parse(a.date);
            let bDateTime = Date.parse(b.date);
            return bDateTime - aDateTime;
        });
    }).catch((error) => {
        console.log(error);
    });

    $: slideoverStore = $SlideOverStore;

    BreadcrumbStore.set({ path: [
        { url: "/registers", name: "Registers" },
    ] });

    let filter = {};

    function applyFilter() {
        if (filter.date) {
            compliments = compliments.filter(
                (compliments) => Date.parse(compliments.date) === Date.parse(filter.date)
            );
        }

        if (filter.acknowledgement_date) {
            compliments = compliments.filter(
                (compliments) => Date.parse(compliments.acknowledgement_date) === Date.parse(filter.acknowledgement_date)
            );
        }

        if (filter.staff) {
            compliments = compliments.filter(
                (compliments) => compliments.staffs_id === filter.staff
            );
        }

        if (filter.status) {
            compliments = compliments.filter(
                (compliments) => compliments.status === filter.status
            );
        }
    }

    function clearFilter() {
        filter = {};
        compliments = [...stored_compliments];
    }

    function showFilter() {
        SlideOverStore.set({
        label: "Filter",
        show: true,
        props: filter,
        component: ComplimentFilter,
        action_label: "Apply",
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
              Compliment Register
        </h1>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/registers/compliments/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add Compliment</button>
    </div>
</div>

<div>
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
            filename="compliments-register.csv"
            bind:json_data={compliments}
            />
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
                      >Compliment</th
                    >
                    <th
                      scope="col"
                      class="px-4 py-2 text-left text-xs font-medium text-slate-500"
                      >Complimenter</th
                    >
    
                    <th
                      scope="col"
                      class="px-4 py-2 text-left text-xs font-medium text-slate-500"
                      >Staff</th
                    >
                    <th
                      scope="col"
                      class="px-4 py-2 text-left text-xs font-medium text-slate-500"
                      >Date</th
                    >
                    <th
                        scope="col"
                        class="px-4 py-2 text-left text-xs font-medium text-slate-500"
                        >Acknowledgement Date</th
                    >
                    <th
                        scope="col"
                        class="px-4 py-2 text-left text-xs font-medium text-slate-500"
                        >Status</th
                    >
                    <th scope="col" class="relative py-2 pl-3 pr-4 sm:pr-6">
                      <span class="sr-only">View</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-indigo-100 bg-white">
                  {#each compliments as compliment, index (compliment.id)}
                    <tr class="hover:bg-indigo-50">
                      <td
                        class="whitespace-nowrap py-3 px-4 text-sm font-medium text-gray-900"
                        >{@html compliment.description.length > 50 
                            ? compliment.description.slice(0, 50) + '...'
                            : compliment.description}
                      </td
                      >
                      <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                        >{compliment.complimenter}</td
                      >
                      <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                        >{compliment.staff_name ? compliment.staff_name : 'N/A'}</td
                      >
                      <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                        >{compliment.date ? formatDate(compliment.date) : "N/A"}</td
                      >
                      <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                        >{compliment.acknowledgement_date
                          ? formatDate(compliment.acknowledgement_date)
                          : "N/A"}</td
                      >
                      <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                      >{compliment.status ? formatPrettyName(compliment.status) : "N/A"}</td
                    >
                      <td
                        class="relative whitespace-nowrap py-3 px-4 text-right text-sm font-medium"
                      >
                        <a
                          href="/#/registers/compliments/{compliment.id}"
                          class="text-indigo-600 hover:text-indigo-900"
                          >View</a
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