<script>
    

    import { push } from "svelte-spa-router";
  import { jspa } from "@shared/jspa.js";

  import { formatDate, timeAgo } from "@shared/utilities.js";
  
  let service_agreements = [];

  jspa(
    "/ServiceAgreement",
    "listServiceAgreementsByStatus",
    {status: "ended"}
  ).then((result) => {
    service_agreements = result.result;

    // sort service_agreements by service_agreement_end_date
    service_agreements.sort((a, b) => {
      return new Date(b.service_agreement_end_date) - new Date(a.service_agreement_end_date);
    });
    
  });

    
  

</script>

<p>
    These are Service Agreements that have expired, and either need : Renew or
    End Agreement
</p>

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
                >Expired</th
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
                ><a href="/#/clients/{service_agreement.client_id}/details" class="text-indigo-600 hover:text-indigo-900"
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
