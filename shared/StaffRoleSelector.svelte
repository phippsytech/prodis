<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { STAFF_ROLES } from "@shared/constants.js";

    export let staff_role = null;
</script>

<FloatingSelect
    on:change
    bind:value={staff_role}
    label="Staff Role"
    instruction="Choose Staff Role"
    options={STAFF_ROLES}
    hideValidation={true}
    {...$$props}
/>
