<script>
    import { push } from "svelte-spa-router";
    import { PlusIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatDate, formatPrettyName } from "@shared/utilities.js";

    let conflictofinterests = [];

    jspa("/Register/ConflictOfInterest", "listConflictOfInterests", {}).then(
        (result) => {
            conflictofinterests = result.result;
            // sort the conflictofinterests reverse chronologically
            conflictofinterests.sort(function (a, b) {
                let aDateTime = Date.parse(a.date_identified);
                let bDateTime = Date.parse(b.date_identified);
                return bDateTime - aDateTime;
            });
        },
    );

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" },
    { url: "/registers/conflictofinterests", name: "Conflict of Interests" },
    ] });
</script>

<div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Conflict Of Interest Register
        </div>
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

<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Conflicts
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Parties Involved 
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Implementing Staff
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Conflict Date
                        </th>
                        <th scope="col" class="py-2 px-4 text-left text-xs font-medium text-slate-500">
                            Resolution Date
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
                    {#each conflictofinterests as conflict}
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                {conflict.description.length > 50 
                                    ? conflict.description.slice(0, 50) + '...' 
                                    : conflict.description}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {conflict.involved_staff_name}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {conflict.parties_involved ? conflict.parties_involved : 'N/A'}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {formatDate(conflict.date_identified)}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {formatDate(conflict.date_resolved) ? formatDate(conflict.date_resolved) : 'N/A'}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {formatPrettyName(conflict.status)}
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <a href="/#/registers/conflictofinterests/{conflict.id}" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                    <span class="sr-only">, {conflict.resolution}</span>
                                </a>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</div>
