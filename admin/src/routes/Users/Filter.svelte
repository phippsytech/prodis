<script>
  export let filters = [
    { label: "admin", enabled: true },
    { label: "house", enabled: false },
    { label: "house.lead", enabled: false },
    { label: "therapist", enabled: false },
    { label: "house", enabled: false },
    { label: "stakeholder", enabled: false },
    { label: "payroll", enabled: false },
  ];

  function toggleFilter(index) {
    filters[index].enabled = !filters[index].enabled;
  }
</script>

<div class="flex gap-x-2 py-2">
  <span class="text-xs flex items-center opacity-50 font-light">Filter by:</span
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
        class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-400 cursor-pointer"
        on:click={() => toggleFilter(index)}
      >
        {filter.label}
      </span>
    {/if}
  {/each}
</div>
