<script>
  import { onMount } from "svelte";
  import { slide } from "svelte/transition";
  import { flip } from "svelte/animate";
  import { PlusIcon } from "heroicons-svelte/24/outline";
  import Role from "@shared/Role.svelte";
  import { ModalStore } from "@app/Overlays/stores";
  import ServiceAgreement from "./ServiceAgreement.svelte";
  import ServiceAgreementForm from "./ServiceAgreementForm.svelte";
  import { toastError, toastSuccess } from "@shared/toastHelper";
  import { isDate } from "@shared/validators";
  import createStore from "@shared/createStore";

  import ServiceAgreementSupportItem from "./ServiceAgreementSupportItem.svelte";
//   import ServiceCombo from "@app/routes/Service/ServiceCombo.svelte";
//   import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
//   import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
//   import PlanManagerCombo from "@app/routes/Accounts/PlanManagers/PlanManagerCombo.svelte";

  export let client_id;

  let props = {};

  let participant_id = client_id; // progressively renaming client_id to participant_id
  let show_inactive_service_agreements = false;

  let service_agreement = {
    client_id: client_id, // TODO: remove this line once client_id is depricated
    participant_id: participant_id,
  };

  export const ServiceAgreementStore = createStore(
    "/Participant/ServiceAgreement",
    {
      list: "listServiceAgreementsByParticipantId",
      add: "addServiceAgreement",
      update: "updateServiceAgreement",
      delete: "deleteServiceAgreement",
    }
  );

  $: $ServiceAgreementStore,
    ServiceAgreementStore.set(
      $ServiceAgreementStore
        .slice()
        .sort((a, b) =>
          a.is_active === b.is_active ? 0 : a.is_active ? -1 : 1
        )
    );

  let hasInactiveAgreements = false;

  // Reactive statement to update hasInactiveAgreements based on the store's content
  $: {
    hasInactiveAgreements = $ServiceAgreementStore.some(
      (agreement) => !agreement.is_active
    );
  }

  onMount(() => {
    ServiceAgreementStore.load({ participant_id });
  });

  function showServiceAgreement(service_agreement) {
    ModalStore.set({
      label: "Add Service Agreement",
      show: true,
      props: service_agreement,
      component: ServiceAgreementForm,
      action_label: "Add",
      action: () => addServiceAgreement(service_agreement),
    });
  }

  function addServiceAgreement(service_agreement) {
    if (
      !service_agreement.service_agreement_signed_date ||
      !isDate(service_agreement.service_agreement_signed_date)
    ) {
      toastError("Signed Date is not a valid date");
      return;
    }

    if (
      !service_agreement.service_agreement_end_date ||
      !isDate(service_agreement.service_agreement_end_date)
    ) {
      toastError("End Date is not a valid date");
      return;
    }

    ServiceAgreementStore.add(service_agreement);
  }
</script>

<div class="mb-2">
  <div class="flex justify-between sm:items-center mt-6 mb-1">
    <div class="flex sm:flex-row flex-col sm:items-center">
      <h3 class="text-slate-800 font-bold mx-2">Pending Service Agreements</h3>
      <Role roles={["serviceagreement.modify"]}>
        <button
          class="text-xs text-indigo-600 hover:underline text-left mx-2"
          on:click={() => showServiceAgreement(service_agreement)}
        >
          <PlusIcon class="w-4 h-4 inline" /> Add Service Agreement
        </button>
      </Role>
    </div>

    

  </div>




<ServiceAgreementSupportItem />













  {#each $ServiceAgreementStore as agreement, index (agreement.id)}
    <div
      animate:flip={{ duration: 350 }}
      in:slide={{ duration: 200 }}
      out:slide={{ duration: 200 }}
    >
      {#if agreement.is_active || (show_inactive_service_agreements && !agreement.is_active)}
        <ServiceAgreement
          service_agreement={agreement}
          {ServiceAgreementStore}
        />
      {/if}
    </div>
  {/each}
</div>
