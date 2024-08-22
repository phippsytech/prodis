<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
    import SupportItemsSelect from "@app/routes/Settings/SupportCatalogue/SupportItemsSelect.svelte";

    export let service = {};

    let max_rate;

    let over_max_rate;
    $: {
        over_max_rate = service.rate <= max_rate;
    }
</script>

<FloatingInput
    bind:value={service.name}
    label="Service name"
    placeholder="eg: Behaviour Management Plan"
    autocomplete="off"
/>
<FloatingInput
    bind:value={service.code}
    label="Service code"
    placeholder="eg: BMP"
    autocomplete="off"
/>
<FloatingInput
    bind:value={service.billing_code}
    label="Billing code"
    placeholder="eg: 11_023_0110_7_3"
    autocomplete="off"
/>
<SupportItemsSelect
    bind:value={service.billing_code}
    bind:unit={service.billing_unit}
    bind:max_rate
    bind:location={service.location}
    bind:name={service.name}
/>

<FloatingSelect
    bind:value={service.location}
    label="Location"
    instruction="Choose Location"
    options={[
        {
            option: "Australian Capital Territory",
            value: "act",
        },
        {
            option: "New South Wales",
            value: "nsw",
        },
        {
            option: "Northern Territory",
            value: "nt",
        },
        {
            option: "Queensland",
            value: "qld",
        },
        {
            option: "South Australia",
            value: "sa",
        },
        {
            option: "Tasmania",
            value: "tas",
        },
        {
            option: "Victoria",
            value: "vic",
        },
        {
            option: "Western Australia",
            value: "wa",
        },
        {
            option: "Remote",
            value: "remote",
        },
        {
            option: "Very Remote",
            value: "very_remote",
        },
    ]}
    hideValidation={true}
/>

<FloatingInput
    bind:value={service.rate}
    label={service.billing_unit === "each"
        ? `Rate for provision (${max_rate})`
        : service.billing_unit === "kms"
          ? "Rate per kilometer"
          : `Rate for ${service.billing_unit} (Max: $${max_rate})`}
    placeholder="eg: 121.21"
    inputmode="decimal"
    autocomplete="off"
    bind:valid={over_max_rate}
/>

<FloatingSelect
    bind:value={service.budget_display}
    label="Budget Display"
    instruction="Choose budget display type"
    options={[
        {
            option: "Total",
            value: "total",
        },
        {
            option: "Weekly",
            value: "weekly",
        },
    ]}
    hideValidation={true}
/>


<!-- <Toggle
    bind:value={service.record_travelled_kilometers}
    label_on="Recording travelled kilometers"
    label_off="Not recording travelled kilometers"
/> -->
