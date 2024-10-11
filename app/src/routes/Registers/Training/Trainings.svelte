<script>
    import { push } from "svelte-spa-router";
    import { PlusIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let trainings = [];

    let breadcrumbs_path = [];
    let breadcrumbs_action = {
        icon: PlusIcon,
        event: () => push("/registers/trainings/add"),
    };

    jspa("/Register/Training", "listTrainings", {}).then((result) => {
        trainings = result.result;
        // sort the trainings reverse chronologically
        trainings.sort(function (a, b) {
            let aDateTime = Date.parse(a.date_identified);
            let bDateTime = Date.parse(b.date_identified);
            return bDateTime - aDateTime;
        });
    });

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });
</script>

<!--            
    <Breadcrumbs path={breadcrumbs_path} target="training Register" action={breadcrumbs_action} /> -->
<div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Training Register
        </div>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/registers/trainings/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Add training</button
        >
    </div>
</div>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Open trainings
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each trainings as training, index (training.id)}
        {#if training.status == "open"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/trainings/" + training.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {training.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {training.archived == 1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{training.date_identified}</span>
                    <span class="text-xs">#{training.id}</span>
                </div>

                <div>
                    <span class="font-bold">{training.type}</span><br />
                    <span class="text-sm">{training.description}</span>
                </div>
            </li>
        {/if}
    {/each}
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Closed trainings
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each trainings as training, index (training.id)}
        {#if training.status == "closed"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                on:click={() => push("/registers/trainings/" + training.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {training.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {training.archived == 1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{training.date_identified}</span>
                    <span class="text-xs">#{training.id}</span>
                </div>
                <div>
                    <span class="font-bold">{training.type}</span><br />
                    <span class="text-sm">{training.resolution}</span>
                </div>
            </li>
        {/if}
    {/each}
</ul>
