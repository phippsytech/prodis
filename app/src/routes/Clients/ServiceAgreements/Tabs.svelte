<script>
  import { createEventDispatcher } from "svelte";

  const dispatch = createEventDispatcher();

  export let tabs = [];

  export function setActiveTab(index) {
    tabs = tabs.map((tab, i) => ({
      ...tab,
      active: i === index,
    }));
    dispatch("tabSelected", { index, tab: tabs[index] });
  }
</script>

<div>
  <nav class="-mb-px flex mx-4" aria-label="Tabs">
    {#each tabs as { name, count, active }, i}
      <button
        type="button"
        on:click={() => setActiveTab(i)}
        class="flex whitespace-nowrap px-1 text-sm font-medium px-3 py-1 {active
          ? ' text-slate-800 font-semibold rounded-md rounded-b-none border border-b-transparent  border-slate-200 bg-white '
          : '  border-transparent text-gray-500  hover:text-indigo-600'}"
      >
        {name}
        {#if count}
          <span
            class="ml-3 hidden rounded-full px-2.5 py-0.5 text-xs font-medium {active
              ? 'bg-indigo-100 text-indigo-600'
              : 'bg-gray-100 text-gray-900'} md:inline-block">{count}</span
          >
        {/if}
      </button>
    {/each}
  </nav>
</div>
