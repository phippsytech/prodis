<script>
    import Select from "svelte-select";
    import { beforeUpdate } from "svelte";

    export let values = [];  // Array to store multiple selected IDs
    export let label = "Label";
    export let items = [];   // List of items with label and value properties
    export let readOnly = false;
    export let placeholderText = "Select or start typing ...";

    // Compute the selected labels based on selected values (IDs)
    $: selectedLabels = getLabelsFromValues(values);

    function getLabelsFromValues(values) {
        if (!items || items.length === 0) {
            return ["No items available"];
        }
        const selectedItems = items.filter(item => values.includes(item.value));
        return selectedItems.length > 0 ? selectedItems.map(item => item.label) : ["No selection"];
    }

    // Validate the items structure before the component updates
    beforeUpdate(() => {
        if (!Array.isArray(items) || !items.every(item => item.label && item.value !== undefined)) {
            console.warn("Invalid items structure. Each item must have a 'label' and 'value' property.");
        }
    });

    $: console.log("Selected values in FloatingMultiCombo:", values);  // Debugging log for the selected values
</script>

{#if readOnly}
    <div class="bg-white rounded-sm px-4 py-2 mb-2" role="status" aria-label={label}>
        <div class="text-xs opacity-50">{label}</div>
        {#each selectedLabels as selectedLabel}
            <div>{selectedLabel || "-"}</div>
        {/each}
    </div>
{:else}
    <div class="rounded-md shadow-sm ring-1 ring-inset ring-indigo-100 bg-white mb-0 pb-1.5 mb-2">
        <h3 class="px-3 pt-2 text-xs text-gray-500">{label}</h3>

        <Select
            multiple={true} 
            items={items} 
            bind:value={values} 
            placeholder={placeholderText}
            containerStyles="border:none; margin:-4px 0.75rem; padding:0 0; min-height:34px;width:auto;"
            --list-position="fixed"
        />
    </div>
{/if}