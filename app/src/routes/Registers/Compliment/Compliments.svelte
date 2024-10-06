<script>
    import { push } from "svelte-spa-router";
    import { PlusIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatPrettyName, formatDate } from "@shared/utilities.js";

    let compliments = [];

    let breadcrumbs_path = [];
    let breadcrumbs_action = {
        icon: PlusIcon,
        event: () => push("/registers/compliments/add"),
    };

    jspa("/Register/Compliment", "listCompliments", {}).then((result) => {
        compliments = result.result;
        // sort the compliments reverse chronologically
        compliments.sort(function (a, b) {
            let aDateTime = Date.parse(a.date);
            let bDateTime = Date.parse(b.date);
            return bDateTime - aDateTime;
        });
    }).catch((error) => {
        console.log(error);
    });

    BreadcrumbStore.set({ path: [
        { url: "/registers", name: "Registers" },
        { url: "/registers/compliments", name: "Compliments" },
    ] });
    
</script>

<div class="sm:flex sm:items-center mb-4 mt-2">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Compliment Register
        </div>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/registers/compliments/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add compliment</button>
    </div>
</div>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2 mt-5">
    Open compliments
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each compliments as compliment, index (compliment.id)}
        {#if compliment.status == "not_acknowledged"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/compliments/" + compliment.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {compliments.length - 1 == index ? 'rounded-b-lg' : ''} border-b border-gray-200 w-full"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{formatDate(compliment.date)}</span>
                </div>
                <div>
                    <span class="font-bold text-xs">From: {compliment.complimenter} </span><br />
                    <span class="font-bold text-xs">To: {compliment.staff_name} </span><br />
                    
                    <div class="mt-2">
                        <span class="font-bold text-xs">Compliment</span><br />
                        <blockquote class="text-xs italic font-semibold">
                            <p>{compliment.description}</p>
                        </blockquote>
                    </div>
                </div>
            </li>
        {/if}
    {/each}
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Acknowledged compliments
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each compliments as compliment, index (compliment.id)}
        {#if compliment.status == "acknowledged"}
            <li
                in:slide={{ duration: 200 }}
                on:click={() => push("/registers/compliments/" + compliment.id)}
                out:slide|local={{ duration: 200 }}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer border-b border-gray-200 w-full {compliments.length - 1 == index ? 'rounded-b-lg' : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{formatDate(compliment.date)}</span>
                </div>
                <div>
                    <span class="font-bold text-xs">From: {compliment.complimenter} </span><br />
                    <span class="font-bold text-xs">To: {compliment.staff_name} </span><br />
                    
                    <div class="mt-2">
                        <span class="font-bold text-xs">Compliment</span><br />
                        <blockquote class="text-xs italic font-semibold">
                            <p>{compliment.description}</p>
                        </blockquote>
                    </div>
                    
                    <div class="mt-2">
                        <span class="font-bold text-xs">Action Taken:</span><br />
                        <blockquote class="text-xs italic font-semibold">
                            {compliment.action_taken}
                        </blockquote>    
                    </div>
                </div>
            </li>
        {/if}
    {/each}
</ul>