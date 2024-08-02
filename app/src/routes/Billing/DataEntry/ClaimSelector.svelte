<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

    export let claim_type = "";
    export let cancellation_reason = null;
    export let readOnly = false;

    let claim_types = [
        { option: "Direct Service (In-Person with Participant)", value: "" },
        { option: "Cancellation", value: "CANC" },
        { option: "NDIA Required Report", value: "REPW" },
        { option: "Provider Travel", value: "TRAN" },
        {
            option: "Non-Face-to-Face Services (Did not see the participant)",
            value: "NF2F",
        },
    ];

    // Note: cancellation reason wording used to start with No show.
    // I've removed "No Show" because it doesn't work for cases where you
    // physically visit the client's house and they are there, but cancel due to ...
    let cancellation_reasons = [
        { option: "Due to health reason", value: "NSDH" },
        { option: "Due to family issues", value: "NSDF" },
        { option: "Due to unavailability of transport", value: "NDST" },
        { option: "Other", value: "NSDO" },
    ];

    $: {
        if (claim_type != "CANC") {
            cancellation_reason = null;
        }
    }
</script>

<FloatingSelect
    bind:value={claim_type}
    label="Claim Type"
    instruction="Claim type of the service provided"
    options={claim_types}
    hideValidation={true}
    {readOnly}
/>

{#if claim_type == "CANC"}
    <FloatingSelect
        bind:value={cancellation_reason}
        label="Cancellation Reason"
        instruction="Choose reason for cancellation"
        options={cancellation_reasons}
        hideValidation={true}
        {readOnly}
    />
{/if}
