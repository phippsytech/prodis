<script>
    import Select from "svelte-select";
    import { onMount } from "svelte";

    export let values = [];  // Array of staff IDs (e.g., [100, 101, 102])
    export let label = "Label";
    export let items = [];   // List of items with { label, value } properties
    export let readOnly = false;
    export let placeholderText = "Select or start typing ...";

    let selectedItems = []; // This will hold the { value, label } objects

    // onMount will convert the passed staff IDs into the correct objects format
    onMount(() => {
        if (values.length > 0 && items.length > 0) {
            values.forEach(id => {
                const matchedItem = items.find(item => item.value == id);
                if (matchedItem) {
                    selectedItems.push(matchedItem);
                }
            });
        }
    });

    function updateValues() {
        // When items are selected, update the `selectedItems` array
        values = selectedItems.map(item => item.value);
    }
</script>

{#if readOnly}
    <div class="bg-white rounded-sm px-4 py-2 mb-2" role="status" aria-label={label}>
        <div class="text-xs opacity-50">{label}</div>
        {#each selectedItems as selectedItem}
            <div>{selectedItem.label || "-"}</div>
        {/each}
    </div>
{:else}
    <div class="rounded-md shadow-sm ring-1 ring-inset ring-indigo-100 bg-white mb-0 pb-1.5 mb-2">
        <h3 class="px-3 pt-2 text-xs text-gray-500">{label}</h3>

        <Select
            multiple={true}
            items={items}
            bind:value={selectedItems}
            placeholder={placeholderText}
            containerStyles="border:none; margin:-4px 0.75rem; padding:0 0; min-height:34px;width:auto;"
            --list-position="fixed"
            on:select={updateValues}
        />
    </div>
{/if}
