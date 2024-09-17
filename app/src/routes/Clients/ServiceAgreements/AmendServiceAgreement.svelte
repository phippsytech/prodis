<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import Role from "@shared/Role.svelte";
    import Container from "@shared/Container.svelte";
    import createStore from "@shared/createStore";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";

    import { formatDate } from "@shared/utilities.js";

    import AmendServiceAgreementServiceItem from "./AmendServiceAgreementServiceItem.svelte";

    export let props = {};

    export let params;
    let service_agreement = {};
    let service_agreement_id = params.service_agreement_id;

    let ParticipantServiceStore = createStore(
        "/Participant/ServiceBooking",
        {
            list: "listServiceBookingsByServiceAgreementId",
        },
        {},
    );

    function getServiceAgreement(service_agreement_id) {
        return jspa("/Participant/ServiceAgreement", "getServiceAgreement", {
            id: service_agreement_id,
        }).then((result) => {
            return result.result;
        });
    }

    onMount(async () => {
        service_agreement = await getServiceAgreement(service_agreement_id);
        console.log(service_agreement);
        ParticipantServiceStore.load({
            service_agreement_id: service_agreement_id,
        });
    });
</script>

<div class="bg-white px-4 py-2">
    <div>
        <div
            class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
        >
            Amend Service Agreement
        </div>
        <p>
            For Participant from {formatDate(
                service_agreement.service_agreement_signed_date,
            )}
            to {formatDate(service_agreement.service_agreement_end_date)}
        </p>
    </div>

    <FloatingDate
        bind:value={props.service_agreement_amendment_start_date}
        label="Effective Date of Amendment"
    />
</div>

<h3 class="text-slate-800 font-bold mx-2 mt-4">
    Select the services you wish to amend
</h3>
<p class="text-xs px-2 mb-2">
    The new budget for services are calculated based on the remaining budget
    from the original service agreement. If "Re-index" is checked, the budget is
    adjusted to ensure that the remaining hours of service do not change.
</p>

{#each $ParticipantServiceStore as participant_service, index}
    <AmendServiceAgreementServiceItem service={participant_service} />
{/each}

<div class="flex justify-end">
    <button
        on:click={() => push("/clients/" + client.id + "/casenotes/add")}
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Amend Service Agreement</button
    >
</div>
