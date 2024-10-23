<script>
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { formatDate, formatPrettyName } from "@shared/utilities.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { SlideOverStore } from "@app/Overlays/stores.js";
    import ContinuousImprovementFilter from "./ContinuousImprovementFilter.svelte";
    import MiniJSON2CSV from "@shared/MiniJSON2CSV.svelte";

    let continuous_improvements = [];
    let stored_continuous_improvements;
 
    jspa("/Register/ContinuousImprovement", "listContinuousImprovement", {})
        .then((result)=> {
            continuous_improvements = result.result;
            stored_continuous_improvements = [...continuous_improvements];
            continuous_improvements.sort(function (a, b) {
            let aDateTime = Date.parse(a.date);
            let bDateTime = Date.parse(b.date);
            return bDateTime - aDateTime;
        });
        });

    $: slideoverStore = $SlideOverStore;

    BreadcrumbStore.set({ path: [
        { url: "/registers", name: "Registers" },
    ] });
    
    let filter = {};

    function applyFilter(filter) {
        if (filter.implementation_date) {
            continuous_improvements = continuous_improvements.filter(
                (continuous_improvements) => Date.parse(continuous_improvements.implementation_date) === Date.parse(filter.implementation_date)
            );
        }

        if (filter.date_of_suggestion) {
            continuous_improvements = continuous_improvements.filter(
                (continuous_improvements) => Date.parse(continuous_improvements.date_of_suggestion) === Date.parse(filter.date_of_suggestion)
            );
        }

        if (filter.staff) {
            continuous_improvements = continuous_improvements.filter(
                (continuous_improvements) => continuous_improvements.involved_staffs_id === filter.staff
            );
        }

        if (filter.reviewer) {
            continuous_improvements = continuous_improvements.filter(
                (continuous_improvements) => continuous_improvements.implementing_staffs_id === filter.reviewer
            );
        }
    }

    function clearFilter(filter) {
        filter = {};
        continuous_improvements = [...stored_continuous_improvements];
    }

    function showFilter() {
        SlideOverStore.set({
        label: "Filter",
        show: true,
        props: filter,
        component: ContinuousImprovementFilter,
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
          Continuous Improvement Register
        </h1>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/registers/continuousimprovements/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add Continuous Improvements</button>
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
          filename="continuous-improvements-register.csv"
          bind:json_data={continuous_improvements}
        />
    </div>
</nav>



<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8 mt-2">
    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Suggestions
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Staff 
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Reviewer
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Suggestion Date
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Implementation Date
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Status
                        </th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 xs:pr-6">
                            <span class="sr-only">View</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    {#each continuous_improvements as improvement}
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                {@html improvement.suggestion_details.length > 50 
                                    ? improvement.suggestion_details.slice(0, 50) + '...' 
                                    : improvement.suggestion_details}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {improvement.involved_staff_name}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {improvement.implementing_staff_name ? improvement.implementing_staff_name : 'N/A'}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {formatDate(improvement.date_of_suggestion)}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {formatDate(improvement.implementation_date) ? formatDate(improvement.implementation_date) : 'N/A'}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {formatPrettyName(improvement.status)}
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <a href="/#/registers/continuousimprovements/{improvement.id}" class="text-indigo-600 hover:text-indigo-900">
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