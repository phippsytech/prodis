<script>
  import Select from "svelte-select";
  import { jspa } from "@shared/jspa.js";

  export let value = null; // Default to null if no value is provided

  let services = [];
  let items = [];
  let placeholderText = "Select or type service ...";

  jspa("/Service", "listServices", {}).then((result) => {
    // if (!isComponentMounted) return; // Exit if component is unmounted
    services = result.result;
    items = services.map((item) => ({
      label: `${item.code}`,
      value: item.id,
    }));

    // Sort the items
    items.sort((a, b) => a.label.localeCompare(b.label));
  });
</script>

<div
  class="rounded-md shadow-sm ring-1 ring-inset ring-indigo-100 bg-white mb-0 pb-1.5 mb-2"
>
  <h3 class="px-3 pt-2 text-xs text-gray-500">Service</h3>

  <Select
    {items}
    {value}
    placeholder={placeholderText}
    bind:justValue={value}
    containerStyles="border:none; margin:0 0.75rem; padding:0 0; min-height:34px;width:auto;"
    --list-position="fixed"
  />
</div>
