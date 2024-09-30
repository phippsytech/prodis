<script>
    import { push } from "svelte-spa-router";

    import { PlusIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let feedbacks = [];

    let breadcrumbs_path = [];
    let breadcrumbs_action = {
        icon: PlusIcon,
        event: () => push("/registers/feedbacks/add"),
    };

    jspa("/Register/Feedback", "listFeedbacks", {}).then((result) => {
        feedbacks = result.result;
        // sort the feedbacks reverse chronologically
        feedbacks.sort(function (a, b) {
            let aDateTime = Date.parse(a.date_identified);
            let bDateTime = Date.parse(b.date_identified);
            return bDateTime - aDateTime;
        });
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
            on:click={() => push("/registers/feedbacks/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Add Feedback</button
        >
    </div>
</div>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Open Complaint
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each feedbacks as feedback, index (feedback.id)}
        {#if feedback.status == "open"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/feedbacks/" + feedback.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {feedback.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {feedback.archived == 1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{feedback.date_identified}</span>
                    <span class="text-xs">#{feedback.id}</span>
                </div>

                <div>
                    <span class="font-bold">{feedback.type}</span><br />
                    <span class="text-sm">{feedback.message}</span>
                </div>
            </li>
        {/if}
    {/each}
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Closed Feedback
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each feedbacks as feedback, index (feedback.id)}
        {#if feedback.status == "closed"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/feedbacks/" + feedback.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {feedback.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {feedback.archived == 1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{feedback.date_identified}</span>
                    <span class="text-xs">#{feedback.id}</span>
                </div>
                <div>
                    <span class="font-bold">{feedback.type}</span><br />
                    <span class="text-sm">{feedback.resolution}</span>
                </div>
            </li>
        {/if}
    {/each}
</ul>
