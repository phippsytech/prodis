<script>
  import { push } from "svelte-spa-router";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import Tabs from "./Tabs.svelte";

  import Active from "./Active.svelte";
  import Expiring from "./Expiring.svelte";
  import Expired from "./Expired.svelte";
  import Pending from "./Pending.svelte";

  let service_agreements = [];

  jspa(
    "/Participant/ServiceAgreement",
    "listServiceAgreementsToAmend",
    {}
  ).then((result) => {
    service_agreements = getUniqueServiceAgreements(result.result);
  });

  BreadcrumbStore.set({ path: [{ name: "Service Agreements" }] });

  function getUniqueServiceAgreements(agreements) {
    const uniqueAgreements = [];
    const ids = new Set();

    for (const agreement of agreements) {
      if (!ids.has(agreement.service_agreement_id)) {
        ids.add(agreement.service_agreement_id);
        uniqueAgreements.push(agreement);
      }
    }

    return uniqueAgreements;
  }

  function addService() {
    push("/service_agreements/add");
  }

  let selectedTab = { name: "Expired" };
  const tabs = [
    { name: "Active", count: 52, active: false },
    { name: "Expiring Soon", count: 6, active: false },
    { name: "Expired", count: 4, active: true },
    { name: "Pending", active: false },
  ];

  function handleTabSelected(event) {
    selectedTab = event.detail.tab;

    // Update other content based on the selected tab
  }
</script>

<div class="sm:flex sm:items-center mt-4">
  <div class="sm:flex-auto">
    <h1 class="text-2xl font-fredoka-one-regular leading-6 text-indigo-900">
      Service Agreements
    </h1>
    <p class="mt-2 text-sm text-gray-700">
      Track, renew, and monitor the status of service agreements
    </p>
  </div>
</div>

<Tabs {tabs} on:tabSelected={handleTabSelected} />

{#if selectedTab}
  {#if selectedTab.name === "Active"}
    <Active {service_agreements} />
  {/if}
  {#if selectedTab.name === "Expiring Soon"}
    <Expiring {service_agreements} />
  {/if}
  {#if selectedTab.name === "Expired"}
    <Expired {service_agreements} />
  {/if}
  {#if selectedTab.name === "Pending"}
    <Pending {service_agreements} />
  {/if}
{/if}
