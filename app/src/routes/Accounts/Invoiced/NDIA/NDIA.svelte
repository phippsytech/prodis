<script>
    import { BreadcrumbStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";
    import ReportPanel from "@app/routes/Reports/ReportPanel.svelte";

    BreadcrumbStore.set({
        path: [
            {
                url: "/accounts",
                name: "Accounts",
            },
            {
                url: "/accounts/invoiced",
                name: "Invoiced",
            },
        ],
    });
</script>

<div class="px-2 mb-2">
    <h2
        class="text-2xl font-fredoka-one-regular tracking-tight text-indigo-900"
    >
        NDIA
    </h2>
    <!-- <p>
        Create invoices and payment batch records in Xero using NDIA Remittance
        files. These records can then be used to match and reconcile the
        payments received from NDIA.
    </p> -->
</div>

<ReportPanel
    on:click={() => {
        push("/accounts/invoiced/ndia/paymentrequeststatus");
    }}
    label="Upload Payment Request Status"
>
    Upload the current Payment Request CSV file from the Provider Portal to keep
    track of payment requests and handle any payment requests that have been
    rejected.
</ReportPanel>

<ReportPanel
    on:click={() => {
        push("/accounts/invoiced/ndia/remittance");
    }}
    label="Remittance"
>
    Create payment batches in Xero to match NDIA payments with their invoices.
    This makes it easy to reconcile NDIA payments in Xero.
</ReportPanel>
