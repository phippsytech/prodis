<script>
    import Select from "svelte-select";
    import { onMount } from "svelte";

    export let values = [];  // Array of staff IDs (e.g., [100, 101, 102])
    export let label = "Label";
    export let items = [];   // List of items with { label, value } properties (comes from API)
    export let readOnly = false;
    export let placeholderText = "Select or start typing ...";

    let selectedItems = []; // Array to hold preselected { value, label } objects
    let isLoaded = false;   // Prevent multiple runs

    // Simulating fetching items (in reality, this could be a real API request)
    async function loadItems() {
        // Here, `items` would be fetched from an API, but we're assuming it's passed in as a prop.
        // You could replace this with an actual fetch request.
        return new Promise((resolve) => {
            const checkItemsLoaded = setInterval(() => {
                if (items.length > 0) {
                    clearInterval(checkItemsLoaded);
                    resolve(items);
                }
            }, 100); // Poll every 100ms for items to be populated
        });
    }

    // Function to set initial values based on loaded items
    async function setInitialValues() {
        try {
            await loadItems(); // Wait until items are loaded

            if (values.length > 0 && items.length > 0) {
                selectedItems = values.map(id => items.find(item => item.value == id)).filter(Boolean);
            }

            isLoaded = true;  // Ensure this block runs only once
        } catch (error) {
            console.error("Error loading items:", error);
        }
    }

    // Trigger the initialization of selected items when component mounts
    onMount(async () => {
        if (!isLoaded) {
            await setInitialValues();
        }
    });

    // Function to update values when selected items change
    function updateValues() {
        if (selectedItems && selectedItems?.length > 0) {
            values = selectedItems.map(item => item.value);
        } else {
            values = [];
        }
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
            on:input={updateValues}
        />
    </div>
{/if}
