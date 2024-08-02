<script>
    import { jspa } from "@shared/jspa.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";

    export let params;

    let remittance_csv_name = params.remittance_csv_name;

    let invoices = [];

    SpinnerStore.set({ show: true });
    jspa("/Invoice/NDIA/Remittance", "getPrototype", {
        remittance_csv_name: remittance_csv_name,
    })
        .then((result) => {
            // toastSuccess("Payment Remittance Uploaded")
            invoices = result.result;
            // push('/clients/'+params.id)
        })
        .catch((error) => {
            let error_message = error.error_message;

            toastError(error_message);
        })
        .finally(() => {
            SpinnerStore.set({ show: false });
        });
</script>

<table class="min-w-full divide-y divide-gray-300">
    <thead>
        <tr>
            <th
                scope="col"
                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                >NDIS Number</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Invoice Batch</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Invoice Total</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Amount Paid</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Match?</th
            >
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        {#each invoices as invoice}
            <tr
                class={invoice.invoice_total === invoice.amount_paid
                    ? "text-green-600"
                    : "text-red-600"}
            >
                <td
                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                    >{invoice.ndis_number}</td
                >
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >{invoice.invoice_batch}</td
                >
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >{invoice.invoice_total}</td
                >
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >{invoice.amount_paid}</td
                >
                <td
                    class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                    >{invoice.invoice_total === invoice.amount_paid
                        ? "Yes"
                        : "No"}</td
                >
            </tr>
        {/each}
    </tbody>
</table>
