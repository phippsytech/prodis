<script>
    import ServiceItem from "@app/routes/Clients/ServiceAgreements/Services/NewServiceItem.svelte";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";

    // export let service_id = null;
    export let participant_service_id = null;
    export let service_agreement;

    let participant_service;
    
    onMount(async () => {
        participant_service = await getParticipantService({
            id: participant_service_id,
        });
        service_agreement = await getServiceAgreement({
            id: participant_service.plan_id,
        });
    });

    $: update(participant_service_id);

    async function update(participant_service_id) {
        participant_service = await getParticipantService({
            id: participant_service_id,
        });
        service_agreement = await getServiceAgreement({
            id: participant_service.plan_id,
        });
    }

    function getParticipantService(data) {
        return jspa("/Participant/Service", "getParticipantService", data).then(
            (result) => {
                return convertFieldsToBoolean(result.result, ["is_active", "adjust_weekly_time"]);
            },
        );
    }

    function getServiceAgreement(data) {
        return jspa(
            "/Participant/ServiceAgreement",
            "getServiceAgreement",
            data,
        ).then((result) => {
            return result.result;
        });
    }
</script>

{#if service_agreement && participant_service}
    {#await service_agreement}
        <p>Loading...</p>
    {:then}
        <ServiceItem
            bind:service_agreement
            bind:service={participant_service}
        />
    {:catch error}
        <p>Error loading service agreement: {error.message}</p>
    {/await}
{:else}
    <p>Loading...</p>
{/if}
