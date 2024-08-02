<script>

    import { jspa } from "@shared/jspa.js";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    
    export let value ="sa";

    
</script>

<FloatingSelect
    bind:value={value}
    label="State"
    instruction="Select State"
    options={[
        { option: "Australian Capital Territory", value: "ACT" },
        { option: "New South Wales", value: "NSW" },
        { option: "Northern Territory", value: "NT" },
        { option: "Queensland", value: "QLD" },
        { option: "South Australia", value: "SA" },
        { option: "Tasmania", value: "TAS" },
        { option: "Victoria", value: "VIC" },
        { option: "Western Australia", value: "WA" },
    ]}
/>