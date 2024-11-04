<script>
  import { onMount } from "svelte";
  import { ModalStore } from "@app/Overlays/stores";
  import ServiceAgreement from "./ServiceAgreement.svelte";
  import ServiceAgreementForm from "./ServiceAgreementForm.svelte";
  import { toastError, toastSuccess } from "@shared/toastHelper";
  import { isDate } from "@shared/validators";
  import createStore from "@shared/createStore";

  import ServiceAgreementSupportItem from "./ServiceAgreementSupportItem.svelte";

  import Tabs from "./Tabs.svelte";
  import Draft from "./Draft.svelte";
  import Active from "./Active.svelte";
  import Ended from "./Ended.svelte";
  import Pending from "./Pending.svelte";

  export let client_id;

  let participant_id = client_id; // progressively renaming client_id to participant_id
  let tabsComponent;

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

  let selectedTab = { name: "Active" };
  const tabs = [
    // { name: "Active", count: 52, active: true },

    { name: "Draft", active: false },
    { name: "Pending", active: false },
    { name: "Active", active: true },
    { name: "Ended", active: false },
  ];

  function handleTabSelected(event) {
    selectedTab = event.detail.tab;
  }

  function handleRenewed() {
    const draftIndex = tabs.findIndex((tab) => tab.name === "Draft");
    if (draftIndex !== -1) {
      tabsComponent.setActiveTab(draftIndex);
    }
  }
</script>

<div class="mb-2">
  <div class="flex justify-between sm:items-center mt-6 mb-1">
    <div class="flex sm:flex-row flex-col sm:items-center">
      <h3 class="text-slate-800 font-bold mx-2">Service Agreements</h3>
    </div>
    <div class="flex sm:flex-row flex-col sm:items-center"></div>
  </div>

  <Tabs bind:this={tabsComponent} {tabs} on:tabSelected={handleTabSelected} />

  <div class="bg-white rounded-md border border-slate-200">
    {#if selectedTab}
      {#if selectedTab.name === "Draft"}
        <Draft {ServiceAgreementStore} {participant_id} />
      {/if}
      {#if selectedTab.name === "Pending"}
        <Pending {ServiceAgreementStore} />
      {/if}
      {#if selectedTab.name === "Active"}
        <Active  on:renewed={handleRenewed} {ServiceAgreementStore} />
      {/if}

      {#if selectedTab.name === "Ended"}
        <Ended on:renewed={handleRenewed} {ServiceAgreementStore} />
      {/if}
    {/if}
  </div>
</div>
