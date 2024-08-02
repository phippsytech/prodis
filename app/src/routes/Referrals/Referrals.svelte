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

    BreadcrumbStore.set({ path: [{ name: "Referrals" }] });

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
            Referrals
        </h1>
        <p>
            This section will display referrals that need to create participants
            and service agreements. Referral data comes via a Referral form
            hosted on the business website.
        </p>
    </div>
</div>
