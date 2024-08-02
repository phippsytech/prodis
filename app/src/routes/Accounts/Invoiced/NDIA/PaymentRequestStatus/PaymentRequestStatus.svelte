<script>
    import PaymentRequestStatusImporter from "./PaymentRequestStatusImporter.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";

    import { onMount } from "svelte";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";
    import { formatDate } from "@shared/utilities.js";

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

            {
                url: "/accounts/invoiced/ndia",
                name: "NDIA",
            },
        ],
    });

    let payment_request = [];

    function handleUpload() {
        listPaymentRequestStatus();
    }

    function listPaymentRequestStatus() {
        return; // temporarily disabling this

        jspa(
            "/Invoice/NDIA/PaymentRequestStatus",
            "listPaymentRequestStatus",
            {},
        )
            .then((result) => {
                // toastSuccess("Payment Remittance Uploaded")
                payment_request = result.result;
                // push('/clients/'+params.id)
            })
            .catch((error) => {
                let error_message = error.error_message;

                toastError(error_message);
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    listPaymentRequestStatus();

    function generateInvoicesAndBatchPayments() {
        SpinnerStore.set({ show: true });
        jspa("/Invoice/NDIA/PaymentRequestStatus", "processErrors", {})
            .then((result) => {
                toastSuccess("Invoices and Batch Payments have been generated");
                // payment_request=result.result
                payment_request = [];
                // push('/clients/'+params.id)
            })
            .catch((error) => {
                let error_message = error.error_message;

                toastError(error_message);
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    function formatErrorMessage(error_message) {
        // return error_message;
        // the error_message is a string with semicolons.  Split it into an array and return the first element
        return error_message.split(";")[1];
        // return error_message.replace(/(?:\r\n|\r|\n)/g, "<br>");
    }
</script>

<div class="px-2 mb-2">
    <h2
        class="text-xl sm:text-2xl font-fredoka-one-regular sm:tracking-tight"
        style="color:#220055;"
    >
        Upload Payment Request Status
    </h2>
    <p>
        Upload the current Payment Request CSV file from the <a
            class="text-indigo-600 underline"
            href="https://myplace.ndis.gov.au/supplier/paymentrequest/searchPaymentRequests"
            >Provider Portal</a
        > to keep track of payment requests and handle any payment requests that
        have been rejected.
    </p>
</div>

<PaymentRequestStatusImporter on:uploaded={handleUpload} />

{#if 1 == 2}
    <ul role="list" class=" bg-white rounded rounded-lg">
        {#each payment_request as payment, index (index)}
            <div class="text-xs">
                {payment.payment_request_id}

                <span class="text-sm italic">
                    {payment.payment_request_status == "ERROR"
                        ? " - " + formatErrorMessage(payment.error_message)
                        : ""}
                </span>
            </div>
            <li
                in:slide|global={{ duration: 150, delay: index * 10 }}
                class="relative flex justify-between border-t-gray-50 border-t py-1 px-5 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 {payment.payment_request_status ==
                'ERROR'
                    ? 'text-red-800'
                    : ''}"
            >
                <!-- <span>{payment.abn_of_support_provider}</span> -->

                <!-- <span>{payment.capped_price}</span> -->
                <span>{payment.claim_reference}</span>

                <!-- <span>{payment.ndis_number}</span> -->
                <!-- <span>{payment.participant_name}</span> -->
                <span>{payment.supports_delivered_from}</span>
                <!-- <span>{payment.supports_delivered_to}</span> -->
                <!-- <span>{payment.registration_number}</span> -->
                <span>{payment.support_number}</span>
                <span>{payment.claim_type}</span>
                <span>{payment.cancellation_reason}</span>
                <span>{payment.quantity}</span>
                <span>{payment.unit_price}</span>

                <span class="text-sm"
                    >{new Intl.NumberFormat("en-AU", {
                        style: "currency",
                        currency: "AUD",
                    }).format(payment.paid_total_amount)}</span
                >
            </li>
        {/each}
    </ul>
{/if}

<!-- {#if payment_request.length > 0} -->
<div class="p-4 flex justify-end">
    <button
        on:click={() => generateInvoicesAndBatchPayments()}
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Process Errors</button
    >
</div>
<!-- {/if} -->
