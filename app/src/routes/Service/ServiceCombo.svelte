<script>
  import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
  import { jspa } from "@shared/jspa.js";

  export let value = null; // Default to null if no value is provided

  let items = [];

  jspa("/Service", "listServices", {}).then((result) => {
    // Map and sort in one step
    items = result.result
      .map((item) => ({
        label: `${item.code}`,
        value: item.id,
      }))
      .sort((a, b) => a.label.localeCompare(b.label));
  });
</script>

<FloatingCombo
  label="Service"
  {items}
  bind:value
  placeholderText="Select or type service ..."
  on:change
  on:clear
/>
