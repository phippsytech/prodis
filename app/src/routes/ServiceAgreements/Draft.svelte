<script>


    import { push } from "svelte-spa-router";
  import { jspa } from "@shared/jspa.js";
  
  import { formatDate, timeAgo } from "@shared/utilities.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  document.title = "Draft Service Agreements";
  BreadcrumbStore.set({ path: [{ name: "Service Agreements", url: "/#/serviceagreements" }] });
  


  let service_agreements = [];

  jspa(
    "/ServiceAgreement",
    "listServiceAgreementsByStatus",
    {status: "draft"}
  ).then((result) => {
    service_agreements = result.result;
    console.log(service_agreements);
  });

  


</script>


<h1 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Draft Service Agreements
  </h1>

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
                >Last Updated</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Renewal</th
            >
            

        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        {#each service_agreements as service_agreement}
        <tr>
            <td
                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                >
                <a href="/#/clients/{service_agreement.client_id}/details" class="text-indigo-600 hover:text-indigo-900"
                    >{service_agreement.client_name}</a
                >
                
                </td
            >
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                >{timeAgo(service_agreement.updated)}</td
            >
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                >{#if service_agreement.parent_id}Renewal{:else}New{/if}</td
            >
            

        </tr>
{/each}
        <!-- More people... -->
    </tbody>
</table>
