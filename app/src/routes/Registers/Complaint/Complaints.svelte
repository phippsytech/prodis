<script>
    import { push } from "svelte-spa-router";

    import { PlusIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import RTEView from "@shared/RTE/RTEView.svelte";

    let complaints = [];

    let breadcrumbs_path = [];
    let breadcrumbs_action = {
        icon: PlusIcon,
        event: () => push("/registers/feedbacks/add"),
    };

    jspa("/Register/Complaint", "listComplaints", {}).then((result) => {
        complaints = result.result;
        console.log(complaints);
        // sort the feedbacks reverse chronologically
        complaints.sort(function (a, b) {
            let aDateTime = Date.parse(a.date_identified);
            let bDateTime = Date.parse(b.date_identified);
            return bDateTime - aDateTime;
        });

        console.log(complaints);
    });

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });
</script>

<!-- <Breadcrumbs path={breadcrumbs_path} target="Feedback Register" action={breadcrumbs_action} /> -->
<div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Complaint Register
        </div>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/registers/complaints/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Add Complaint</button
        >
    </div>
</div>


{#if !complaints.length}
    <div class="ml-3 flex-1 md:flex md:justify-between">
        <p class="text-sm text-blue-700">
        <span class="font-bold">No complainats to display.</span>
        </p>
    </div>
{/if}


<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Open
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each complaints as complaint, index (complaint.id)}
        {#if complaint.status == "open"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/complaints/" + complaint.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {complaint.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {complaint.archived == 1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{complaint.created}</span>
                    <span class="text-xs">#{complaint.id}</span>
                </div>
                <div>
                    <span class="font-bold">{complaint.complaint_type}</span><br />
                    <RTEView bind:content={complaint.details} />
                </div>
            </li>
        {/if}
    {/each}
</ul>
<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Closed
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each complaints as complaint, index (complaint.id)}
        {#if complaint.status == "closed"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/complaints/" + complaint.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {complaint.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {complaint.archived == 1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{complaint.created}</span>
                    <span class="text-xs">#{complaint.id}</span>
                </div>
                <div>
                    <span class="font-bold">{complaint.complaint_type}</span><br />
                    <span class="text-sm">{complaint.details}</span>
                </div>
            </li>
        {/if}
    {/each}
</ul>

