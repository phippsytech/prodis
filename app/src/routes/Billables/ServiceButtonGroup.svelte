<script>
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";

    export let service_booking_id = null;
    export let serviceBookingList = [];
    export let readOnly = false;


</script>

{#if serviceBookingList.length > 0}
    <div class="p-2">
        <!-- Services -->
        <RadioButtonGroup
            bind:value={service_booking_id}
            label="Services"
            options={serviceBookingList}
            {readOnly}
        />
    </div>
{/if}
