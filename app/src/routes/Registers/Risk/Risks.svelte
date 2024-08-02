<script>
    import { push } from "svelte-spa-router";
    import { PlusIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let risks = [];

    let breadcrumbs_path = [];
    let breadcrumbs_action = {
        icon: PlusIcon,
        event: () => push("/registers/risks/add"),
    };

    jspa("/Register/Risk", "listRisks", {}).then((result) => {
        risks = result.result;
        // sort the risks reverse chronologically
        risks.sort(function (a, b) {
            let aDateTime = Date.parse(a.date_identified);
            let bDateTime = Date.parse(b.date_identified);
            return bDateTime - aDateTime;
        });
    });

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });
</script>

<!--            
    <Breadcrumbs path={breadcrumbs_path} target="Risk Register" action={breadcrumbs_action} /> -->
<div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Risk Register
        </div>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/registers/risks/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Add Risk</button
        >
    </div>
</div>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Open Risks
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each risks as risk, index (risk.id)}
        {#if risk.status == "open"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/risks/" + risk.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {risk.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {risk.archived == 1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{risk.date_identified}</span>
                    <span class="text-xs">#{risk.id}</span>
                </div>

                <div>
                    <span class="font-bold">{risk.type}</span><br />
                    <span class="text-sm">{risk.description}</span>
                </div>
            </li>
        {/if}
    {/each}
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Closed Risks
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each risks as risk, index (risk.id)}
        {#if risk.status == "closed"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/risks/" + risk.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {risk.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {risk.archived == 1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{risk.date_identified}</span>
                    <span class="text-xs">#{risk.id}</span>
                </div>
                <div>
                    <span class="font-bold">{risk.type}</span><br />
                    <span class="text-sm">{risk.resolution}</span>
                </div>
            </li>
        {/if}
    {/each}
</ul>
