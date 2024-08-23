<script>
  import { onMount, afterUpdate } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import Select from "svelte-select";
  import ErrorMessage from "@shared/PhippsyTech/svelte-ui/ErrorMessage.svelte";

  export let value;
  export let location;
  export let unit = "hour/day/year";
  export let max_rate = 0;
  export let name;

  let mounted = false;
  let support_items = [];
  let items = [];

  let internalValue = null;
  let filterText = "";
  let select; // reference to the select component

  let disabled = false;
  let placeholderText = "Select or type NDIS Support Item ...";

  let showError = false;
  let errorMessage = "";

  // Fetch support items and populate the option list
  onMount(() => {
      jspa("/SupportItem", "listSupportItems", {})
          .then((result) => {
              support_items = result.result;

              items = support_items.map(item => {
                  return {
                      label: `${item.support_item_number} : ${item.support_item_name} (${item.registration_group_name})`,
                      value: item.support_item_number
                  };
              });

              items.sort((a, b) => a.label.localeCompare(b.label));
              mounted = true;

              // set internalValue after items are loaded
              updateInternalValue();
          })
          .catch((error) => {
              //console.error("Error fetching support items:", error);
              errorMessage = "No support items found";
              showError = true;
          });
  });

  function updateInternalValue() {
      if (mounted && value) {
          const matchedItem = items.find(item => item.value === value);
          if (matchedItem) {
              internalValue = matchedItem;
              setSupportItem(matchedItem.value);
              disabled = false;
          } else {
              internalValue = null; // reset internalValue
              disabled = true;
              errorMessage = "Support item for this service might be expired or inactive";
              placeholderText = "";
              showError = true;
          }
      }
  }

  function setSupportItem(support_item_number) {
      const item = support_items.find(
          (item) => item.support_item_number === support_item_number,
      );

      if (item) {
          name = item.support_item_name;

          switch (item.unit) {
              case "H":
                  unit = "hour";
                  break;
              case "E":
                  unit = "each";
                  break;
              case "D":
                  unit = "day";
                  break;
              case "YR":
                  unit = "year";
                  break;
              case "MON":
                  unit = "month";
                  break;
              default:
                  unit = "unknown";
          }

          if (location && item[location] !== undefined) {
              max_rate = item[location];
          }
      }
  }

  // watch for changes to value or location after the component is mounted
  $: if (mounted) {
      updateInternalValue();
  }

  // handle when the user blurs the select dropdown
  function handleBlur() {
      if (internalValue && internalValue.value !== value) {
          value = internalValue.value;
          setSupportItem(internalValue.value);
      }
      filterText = "";
  }

  // ensure internalValue is updated after value changes
  afterUpdate(() => {
      updateInternalValue();
  });
</script>

<div class="rounded-t-md shadow-sm ring-1 ring-inset ring-indigo-100 bg-white mb-0 pb-1.5">
  <h3 class="px-2.5 pt-2 text-xs text-gray-500">NDIS Support Item</h3>

  <Select
      bind:this={select}
      containerStyles="border:none; margin:0 0.75rem; padding:0 0; min-height:34px;width:auto;"
      --list-position="fixed"
      bind:value={internalValue}
      bind:filterText
      on:blur={handleBlur}
      {items}
      placeholder={placeholderText}
      hideEmptyState
      {disabled}
  >
      <div slot="item" let:item>
          {item.label}
      </div>
  </Select>
</div>

{#if showError}
  <ErrorMessage
    message={errorMessage}
    visible={showError}
  />
{:else}
  <div class="text-xs bg-indigo-50 text-gray-500 px-3 py-1 rounded-b-md mb-2">
    Tip: Start typing to search for NDIS Support Item
  </div>
{/if}