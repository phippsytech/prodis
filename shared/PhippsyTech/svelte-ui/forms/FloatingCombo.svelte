<script>
  import Select from "svelte-select";
  import { beforeUpdate } from "svelte";

  export let value = null;
  export let label = "Label";
  export let items = [];
  export let readOnly = false;
  export let placeholderText = "Select or start typing ...";

  $: selectedLabel = getLabelFromValue(value);

  // Function to get the label of the selected value
  function getLabelFromValue(value) {
    if (!items || items.length === 0) {
      return "No items available";
    }
    const selectedItem = items.find((item) => item.value === value);
    return selectedItem ? selectedItem.label : "No selection";
  }

  // Warn if items are incorrectly structured
  beforeUpdate(() => {
    if (
      !Array.isArray(items) ||
      !items.every((item) => item.label && item.value !== undefined)
    ) {
      console.warn(
        "Invalid items structure. Each item must have a 'label' and 'value' property."
      );
    }
  });
</script>

{#if readOnly}
  <div
    class="bg-white rounded-sm px-4 py-2 mb-2"
    role="status"
    aria-label={label}
  >
    <div class="text-xs opacity-50">{label}</div>
    {selectedLabel || "-"}
    <!-- Use selectedLabel with fallback -->
  </div>
{:else}
  <div
    class="rounded-md shadow-sm ring-1 ring-inset ring-indigo-100 bg-white mb-0 pb-1.5 mb-2"
  >
    <h3 class="px-3 pt-2 text-xs text-gray-500">{label}</h3>

    <Select
      containerStyles="border:none; margin:-4px 0.75rem; padding:0 0; min-height:34px;width:auto;"
      --list-position="fixed"
      {items}
      {value}
      placeholder={placeholderText}
      bind:justValue={value}
    >
      <div slot="item" let:item>
        {item.label}
      </div>
    </Select>
  </div>
{/if}
