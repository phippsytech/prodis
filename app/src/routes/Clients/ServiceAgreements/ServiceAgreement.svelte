<script>
  import { DocumentTextIcon } from "heroicons-svelte/24/outline";
  import { formatDate } from "@shared/utilities.js";
  import { isDate } from "@shared/validators";
  import { ModalStore } from "@app/Overlays/stores";
  import { toastError, toastSuccess } from "@shared/toastHelper";
  import Role from "@shared/Role.svelte";
  import ServiceAgreementForm from "./ServiceAgreementForm.svelte";
  import ServiceBookings from "./ServiceBookings/ServiceBookings.svelte";
  import { jspa } from "@shared/jspa.js";
  import { createEventDispatcher } from "svelte";

  export let service_agreement;
  export let ServiceAgreementStore;
  export let is_ended = false;
  export let is_expiring = false;

  const dispatch = createEventDispatcher();

  function editServiceAgreement(service_agreement) {
    ModalStore.set({
      label: "Update Service Agreement",
      show: true,
      props: service_agreement,
      component: ServiceAgreementForm,
      action_label: "Update",
      action: () => updateServiceAgreement(service_agreement),
    });
  }

  function updateServiceAgreement(service_agreement) {
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
    ServiceAgreementStore.updateItem(service_agreement);
  }

  function renewServiceAgreement(service_agreement) {
    jspa("/Participant/ServiceAgreement", "renewServiceAgreement", {service_agreement_id: service_agreement.id})
        .then((result) => {
          // move this inside the renewServiceAgreement function
          toastSuccess("Service Agreement renewed successfully.");
          dispatch("renewed");
        })
        .catch((error) => {
          toastError("<b>Cannot renew</b><br/>A draft service agreement is already in progress.");
          return;
        });
  }
  
</script>

<ul
  class="bg-white border-b border-x rounded-md w-full text-slate-900 mb-2 "
>
  <li
    class="px-4 py-2 w-full cursor-default text-sm border-t rounded-t-md  bg-indigo-500 text-white border-indigo-500"
  >
    <div class="flex justify-between items-center">
      <h3 class="flex items-center font-bold">
        <DocumentTextIcon class="h-4 w-4 inline mr-2" />
        #{service_agreement.id}
        {formatDate(service_agreement.service_agreement_signed_date)} - {formatDate(
          service_agreement.service_agreement_end_date
        )}
        
        <!-- {#if service_agreement.is_active}
          - ACTIVE
           {:else}
          - INACTIVE 
          {/if} -->
      </h3>

      <Role roles={["serviceagreement.modify"]}>
        <div>


          <button
          on:click={() => renewServiceAgreement(service_agreement)}
          class="px-2 bg-indigo-600 rounded-full text-white hover:bg-white text-slate-400 hover:text-indigo-600 cursor-pointer"
        >
          Renew
        </button>

        </div>
      </Role>
    </div>
  </li>

  <ServiceBookings bind:service_agreement />
</ul>
