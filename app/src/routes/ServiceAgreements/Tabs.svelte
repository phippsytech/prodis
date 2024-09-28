<script>
    import { createEventDispatcher } from "svelte";

    const dispatch = createEventDispatcher();

    export let tabs = [];

    function setActiveTab(index) {
        tabs = tabs.map((tab, i) => ({
            ...tab,
            active: i === index,
        }));
        dispatch("tabSelected", { index, tab: tabs[index] });
    }
</script>

<div>
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            {#each tabs as { name, count, active }, i}
                <button
                    type="button"
                    on:click={() => setActiveTab(i)}
                    class="flex whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium {active
                        ? 'border-indigo-500 text-indigo-600'
                        : 'border-transparent text-gray-500 hover:border-gray-200 hover:text-gray-700'}"
                >
                    {name}
                    {#if count}
                        <span
                            class="ml-3 hidden rounded-full px-2.5 py-0.5 text-xs font-medium {active
                                ? 'bg-indigo-100 text-indigo-600'
                                : 'bg-gray-100 text-gray-900'} md:inline-block"
                            >{count}</span
                        >
                    {/if}
                </button>
            {/each}
        </nav>
    </div>
</div>
