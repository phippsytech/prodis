<script>
  import Select from "svelte-select";
  import { jspa } from "@shared/jspa.js";

  export let value = null; // Default to null if no value is provided

  let items = [];

  let placeholderText = "Select or type payer name ...";

  // Fetch planManagers on mount

  jspa("/PlanManager", "listPlanManagers", {}).then((result) => {
    // Map and sort in one step
    items = result.result
      .map((item) => ({
        label: `${item.name}`,
        value: item.id,
      }))
      .sort((a, b) => a.label.localeCompare(b.label));
  });
</script>

<div
  class="rounded-md shadow-sm ring-1 ring-inset ring-indigo-100 bg-white mb-0 pb-1.5 mb-2"
>
  <h3 class="px-3 pt-2 text-xs text-gray-500">Payer</h3>

  <Select
    containerStyles="border:none; margin:0 0.75rem; padding:0 0; min-height:34px;width:auto;"
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
