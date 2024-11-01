<script>
    import FileContractIcon from "@shared/PhippsyTech/svelte-ui/icons/FileContract.svelte";

    import {
        EnvelopeIcon,
        TrashIcon,
        DocumentTextIcon,
        UserIcon,
    } from "heroicons-svelte/24/outline";
    export let service_agreements = [];

    let request = [];
</script>

<p>Pending statuses:</p>
<ul>
    <li>Preparing</li>
    <li>Sent</li>
    <li>Viewed</li>
    <li>In progress</li>
    <li>Signed</li>
    <li>Declined</li>
</ul>

<table class="min-w-full divide-y divide-gray-300">
    <thead>
        <tr>
            <th
                scope="col"
                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                >Participant</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Representative</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Status</th
            >
            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                <span class="sr-only">Action</span>
            </th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        <tr>
            <td
                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                >Lindsay Walton</td
            >

            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                <div class="flex items-center px-2">
                    <span class="mr-4 flex items-center"
                        ><UserIcon class="w-4 h-4 inline mb-1" />
                    </span>

                    <span>
                        <EnvelopeIcon class="w-4 h-4 inline  mb-1" />
                        lindsay.walton@example.com
                    </span>
                </div>
            </td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                >Viewed 3 weeks ago</td
            >
            <td
                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
            >
                <div class="flex">
                    <button
                        on:click={() => getSignatureRequest(request)}
                        class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
                        title="View Document"
                        ><DocumentTextIcon class="h-4 w-4" /></button
                    >

                    {#if request.email}
                        <button
                            on:click={() => emailSignatureRequest(request.id)}
                            class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
                            title="Email Document"
                            ><EnvelopeIcon class="h-4 w-4" /></button
                        >{:else}
                        <span
                            class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-slate-200 transition duration-300"
                            title="Can't Email"
                            ><EnvelopeIcon class="h-4 w-4" /></span
                        >
                    {/if}
                    <button
                        on:click={() => archiveSignatureRequest(request.id)}
                        type="button"
                        class="flex p-1 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
                        title="Delete Document"
                        ><TrashIcon class="h-4 w-4 inline m-0 p-0" /></button
                    >
                </div>
            </td>
        </tr>

        <!-- More people... -->
    </tbody>
</table>
<div class="px-6 py-2 w-full rounded-t-lg text-slate-400 cursor-default">
    <div class="text-sm text-center text-slate-500 p-4 pt-2">
        <div
            class="flex justify-center items-center h-8 w-8 text-slate-300 mx-auto m-2"
        >
            <svg
                data-slot="icon"
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
                    d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"
                ></path>
            </svg>
        </div>
        <div>No pending Service Agreements found</div>
    </div>
</div>
