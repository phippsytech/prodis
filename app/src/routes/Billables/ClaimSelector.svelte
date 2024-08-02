<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

    export let claim_type = "";
    export let cancellation_reason = null;
    export let readOnly = false;

    let claim_types = [
        { option: "Direct Service (In-Person with Participant)", value: "" },
        {
            option: "Non-Face-to-Face Services (Did not see the participant)",
            value: "NF2F",
        },
        { option: "NDIA Required Report", value: "REPW" },
        { option: "Cancellation", value: "CANC" },

        // { option: "Provider Travel", value: "TRAN" },
    ];

    // Note: cancellation reason wording used to start with No show.
    // I've removed "No Show" because it doesn't work for cases where you
    // physically visit the client's house and they are there, but cancel due to ...
    let cancellation_reasons = [
        { option: "Due to health reason", value: "NSDH" },
        { option: "Due to family issues", value: "NSDF" },
        { option: "Due to unavailability of transport", value: "NSDT" },
        { option: "Other", value: "NSDO" },
    ];

    $: {
        if (claim_type != "CANC") {
            cancellation_reason = null;
        }
    }
</script>

<div class="flex flex-col md:flex-row gap-x-2">
    <div class="flex-1">
        <FloatingSelect
            bind:value={claim_type}
            label="Claim Type"
            instruction="Claim type of the service provided"
            options={claim_types}
            hideValidation={true}
            {readOnly}
        />
    </div>

    {#if claim_type == "CANC"}
        <div class="flex-1">
            <FloatingSelect
                bind:value={cancellation_reason}
                label="Cancellation Reason"
                instruction="Choose reason for cancellation"
                options={cancellation_reasons}
                hideValidation={true}
                {readOnly}
            />
        </div>
    {/if}
</div>
