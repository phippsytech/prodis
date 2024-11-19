<script>
  import { push } from "svelte-spa-router";
  import { jspa } from "@shared/jspa.js";

  import { formatDate, timeAgo } from "@shared/utilities.js";

  import { BreadcrumbStore } from "@shared/stores.js";
  document.title = "Active Service Agreements";
  BreadcrumbStore.set({ path: [{ name: "Service Agreements", url: "/#/serviceagreements" }] });
  


  let service_agreements = [];

  jspa("/ServiceAgreement", "listServiceAgreementsByStatus", {
    status: "active",
  }).then((result) => {
    service_agreements = result.result;

    // sort service_agreements by service_agreement_end_date
    service_agreements.sort((a, b) => {
      return (
        new Date(a.service_agreement_end_date) -
        new Date(b.service_agreement_end_date)
      );
    });
  });
</script>

<h1 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Active Service Agreements
</h1>
<!-- 
<nav
  class="bg-white text-slate-300 rounded-lg flex justify-between space-x-2 items-center px-2 py-1 mt-4"
  aria-label="Toolbar"
>
  <div class="flex space-x-2 items-center">
    <button
      class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
      ><span class="sr-only">Filter</span>
      <svg
        data-slot="icon"
        height="1em"
        fill="none"
        stroke-width="1.5"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
        ><path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z"
        ></path></svg
      ></button
    >
  </div>
  <div class="flex space-x-2 items-center">
    <button
      type="button"
      title="Export to CSV"
      aria-label="Export to CSV"
      class="rounded bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-600 hover:text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
      ><svg
        class="w-4 h-4 inline-block -mt-0.5"
        data-slot="icon"
        fill="none"
        stroke-width="1.5"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
        ><path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
        ></path></svg
      ></button
    >
  </div>
</nav> -->

<table class="min-w-full divide-y divide-gray-300">
  <thead>
    <tr>
      <th
        scope="col"
        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
        >Participant</th
      >
      <th
        scope="col"
        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
        >Expires</th
      >
      <th
        scope="col"
        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
        >Action</th
      >
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-200">
    {#each service_agreements as service_agreement}
      <tr>
        <td
          class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
          ><a
            href="/#/clients/{service_agreement.client_id}/details"
            class="text-indigo-600 hover:text-indigo-900"
            >{service_agreement.client_name}</a
          ></td
        >
        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
          >{timeAgo(service_agreement.service_agreement_end_date)}</td
        >
        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
          >To be renewed</td
        >
      </tr>
    {/each}
  </tbody>
</table>
