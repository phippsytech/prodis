<!-- SortableColumn.svelte -->
<script>
    import { createEventDispatcher } from 'svelte';
    import { ChevronUpIcon, ChevronDownIcon } from  "heroicons-svelte/24/outline";

    export let column;
    export let label;
    export let sortColumn;
    export let sortDirection;

    const dispatch = createEventDispatcher();

    function handleSort() {
        dispatch('sort', { column });
    }
</script>

<div class="sortable-column cursor-pointer" on:click={handleSort}>
    {label}
    {#if sortColumn === column}
        {#if sortDirection === 'asc'}
        <ChevronDownIcon class="h-4 w-4 inline" />    
        
        {:else}
        <ChevronUpIcon class="h-4 w-4 inline" />    
        {/if}
    {/if}
</div>
