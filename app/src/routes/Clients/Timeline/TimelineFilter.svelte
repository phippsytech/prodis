<script>
    import { getMonday, getDatePlusDays } from "@shared/utilities.js";

    export let filters = [];

    export let start_date = getMonday();
    export let end_date = getDatePlusDays(start_date, 7);

    function toggleFilter(index) {
        filters[index].enabled = !filters[index].enabled;
    }
</script>

<div class="text-sm mb-1 font-bold opacity-50">Filter</div>
<span class="text-xs flex items-center opacity-50 font-light">Period</span>
<div class="flex justify-between gap-0 sm:gap-2 items-center">
    <div class="flex-grow">
        <div
            class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
        >
            <label class="text-xs opacity-50 p-0 m-0 block">Start Date</label>
            <input
                type="date"
                class="w-full p-0 m-0 outline-none bg-white"
                bind:value={start_date}
            />
        </div>
    </div>

    <div class="flex-grow">
        <div
            class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
        >
            <label class="text-xs opacity-50 p-0 m-0 block">End Date</label>
            <input
                type="date"
                class="w-full p-0 m-0 outline-none bg-white"
                bind:value={end_date}
            />
        </div>
    </div>
</div>

<div class="flex gap-x-2 py-2">
    <span class="text-xs flex items-center opacity-50 font-light"
        >Filter by:</span
    >
    {#each filters as filter, index}
        {#if filter.enabled}
            <span
                class="inline-flex items-center gap-x-1.5 rounded-md bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700 cursor-pointer"
                on:click={() => toggleFilter(index)}
            >
                <svg
                    class="h-1.5 w-1.5 fill-indigo-500"
                    viewBox="0 0 6 6"
                    aria-hidden="true"
                >
                    <circle cx="3" cy="3" r="3" />
                </svg>
                {filter.label}
            </span>
        {:else}
            <span
                class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-400 cursor-pointer"
                on:click={() => toggleFilter(index)}
            >
                {filter.label}
            </span>
        {/if}
    {/each}
</div>
