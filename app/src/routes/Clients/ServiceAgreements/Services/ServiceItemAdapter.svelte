<script>
    import ServiceItem from "@app/routes/Clients/ServiceAgreements/Services/NewServiceItem.svelte";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";

    // export let service_id = null;
    export let participant_service_id = null;

    let participant_service;
    let service_agreement;

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
                return result.result;
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
