<script>
  import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
  import { jspa } from "@shared/jspa.js";

  export let xero_employee_id;

  let readOnly = false;

  let items = [];

  jspa("/Xero", "getEmployees", {})
    .then((result) => {
      items = result.result
        .map((item) => ({
          label: `${item.name}`,
          value: item.id,
        }))
        .sort((a, b) => a.label.localeCompare(b.label));
    })
    .catch(() => {});
</script>

<FloatingCombo
  label="Xero Employee Record"
  {items}
  bind:value={xero_employee_id}
  placeholderText="Select or type employee name ..."
  {readOnly}
/>
