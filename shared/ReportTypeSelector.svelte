<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { REPORT_TYPES } from "@shared/constants.js";

    export let report_type = null;
</script>

<FloatingSelect
    on:change
    bind:value={report_type}
    label="Report Type"
    instruction="Choose Report Type"
    options={REPORT_TYPES}
    hideValidation={true}
    {...$$props}
/>
