<script>
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { formatDate, formatPrettyName } from "@shared/utilities.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let continuous_improvements = [];

    jspa("/Register/ContinuousImprovement", "listContinuousImprovement", {})
        .then((result)=> {
            continuous_improvements = result.result;
            continuous_improvements.sort(function (a, b) {
            let aDateTime = Date.parse(a.date);
            let bDateTime = Date.parse(b.date);
            return bDateTime - aDateTime;
        });
        });
    BreadcrumbStore.set({ path: [
        { url: "/registers", name: "Registers" },
        { url: "/registers/continuousimprovements", name: "Continuous Improvements" },
    ] });
    

</script>

<div class="sm:flex sm:items-center mb-4 mt-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Continuous Improvements Register
        </div>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/registers/continuousimprovements/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add continuous improvements</button>
    </div>
</div>

<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Suggestions
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Involved Staff 
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Implementing Staff
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
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    {#each continuous_improvements as improvement}
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                {improvement.suggestion_details.length > 50 
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
                                    Edit
                                    <span class="sr-only">, {improvement.suggestion_details}</span>
                                </a>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</div>