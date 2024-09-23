<script>
  import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
  import { jspa } from "@shared/jspa.js";

  export let value = null; // Default to null if no value is provided

  let items = [];

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

<FloatingCombo
  label="Payer"
  {items}
  bind:value
  placeholderText="Select or type payer name ..."
/>
