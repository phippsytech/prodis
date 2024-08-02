<script>
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let service_agreements = [];

    jspa(
        "/Participant/ServiceAgreement",
        "listServiceAgreementsToAmend",
        {},
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
</script>

<div class="flex items-center mb-4">
    <div class="flex-auto">
        <h1
            class="text-2xl font-fredoka-one-regular px-4"
            style="color:#220055;"
        >
            Service Agreements
        </h1>
        <p>
            This section is still being worked on. For now you can use it to
            find service agreements that need to be amended, and to manage
            service agreement amendments
        </p>
    </div>
    <button
        on:click={() => addService()}
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add</button
    >
</div>

There are {service_agreements.length} to amend

<table class="table w-full">
    <tr>
        <th class="text-left">#</th>
        <th class="text-left">Participant</th>
        <th class="text-left">Representative</th>
        <th class="text-left">Email</th>
        <th class="text-left">Code</th>
        <th class="text-right">Rate</th>
        <th class="text-right">New Rate</th>
    </tr>
    {#each service_agreements as agreement, index (agreement.participant_service_id)}
        <tr>
            <td>{agreement.service_agreement_id}</td>
            <td>{agreement.participant_name}</td>
            <td>{agreement.representative_name}</td>
            <td>{agreement.representative_email}</td>
            <td>{agreement.code}</td>
            <td class="text-right">{agreement.rate}</td>
            <td class="text-right">{agreement.support_item_rate}</td>
        </tr>
    {/each}
</table>
