<script>
    import { push } from "svelte-spa-router";

    import { PlusIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";

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

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });
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

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Open Conflict Of Interests
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each conflictofinterests as conflictofinterest, index (conflictofinterest.id)}
        {#if conflictofinterest.status == "open"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() =>
                    push(
                        "/registers/conflictofinterests/" +
                            conflictofinterest.id,
                    )}
                class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {conflictofinterest.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {conflictofinterest.archived ==
                1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="list-group-item">
                    <div class="justify-between flex">
                        <span class="text-xs"
                            >{conflictofinterest.date_identified}</span
                        >
                        <span class="text-xs">#{conflictofinterest.id}</span>
                    </div>

                    <div>
                        {@html conflictofinterest.description.replace(
                            /\n/g,
                            "<br />",
                        )}
                    </div>
                </div>
            </li>
        {/if}
    {/each}
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Closed Conflict Of Interests
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each conflictofinterests as conflictofinterest, index (conflictofinterest.id)}
        {#if conflictofinterest.status == "closed"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() =>
                    push(
                        "/registers/conflictofinterests/" +
                            conflictofinterest.id,
                    )}
                class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {conflictofinterest.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {conflictofinterest.archived ==
                1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="list-group-item">
                    <div class="justify-between flex">
                        <span class="text-xs"
                            >{conflictofinterest.date_identified}</span
                        >
                        <span class="text-xs">#{conflictofinterest.id}</span>
                    </div>
                    <div>
                        {@html conflictofinterest.resolution.replace(
                            /\n/g,
                            "<br />",
                        )}
                    </div>
                </div>
            </li>
        {/if}
    {/each}
</ul>

<style>
    .list-group-item {
        max-height: 50px;
        overflow: hidden;
        text-overflow: ellipsis;
        content: "";
        position: relative;
        transition: max-height 0.1s ease;
    }
    .list-group-item:before {
        content: "";
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
        background: linear-gradient(transparent 25px, white);
    }

    .list-group-item.expanded {
        max-height: 10000px;
    }
    .list-group-item.expanded:before {
        background: none;
    }
</style>
