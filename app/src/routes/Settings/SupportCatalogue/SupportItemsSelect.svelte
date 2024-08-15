<script>
  import { jspa } from "@shared/jspa.js";
  import Select from "svelte-select";

  export let value;
  export let location;
  export let unit;
  export let max_rate;
  export let name;

  let mounted = false;
  let support_items = [];
  let option_list = [];

  let internalValue = null;
  let filterText = "";
  let select; // reference to the select component

  // fetch support items and populate the option list
  jspa("/SupportItem", "listSupportItems", {})
      .then((result) => {
          support_items = result.result;

          let selected = false;

          option_list = support_items.map(item => {
              let option = {
                  label: `${item.support_item_number} : ${item.support_item_name} (${item.registration_group_name})`,
                  value: item.support_item_number
              };

              if (value && item.support_item_number === value) {
                  internalValue = option; // set the selected option
                  selected = true;
              }
              return option;
          });

          if (!selected) {
              internalValue = null; // reset if no item is selected
          }

          option_list.sort((a, b) => a.label.localeCompare(b.label));

          mounted = true;
      })
      .catch((error) => {
          console.error("Error fetching support items:", error);
      });

  function setSupportItem(support_item_number) {
      let item = support_items.find(
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

          max_rate = item[location];
      }
  }

  // watch for changes to internalValue and update the selected support item
  $: {
      if (internalValue && mounted) {
          setSupportItem(internalValue.value);
      }
  }

  function handleBlur() {
      if (!internalValue && filterText !== "") {
          internalValue = { value: filterText, label: filterText };
      }
  }
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
    items={option_list}
    placeholder="Select or type NDIS Support Item ..."
    hideEmptyState
>
    <div slot="item" let:item>
        {item.label}
    </div>
</Select>
</div>

<div class="text-xs bg-indigo-50 text-gray-500 px-3 py-1 rounded-b-md mb-2">
Tip: Start typing to search for NDIS Support Item
</div>
