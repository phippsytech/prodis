<script>
    import NDIACSVImporter from "./NDIACSVImporter.svelte";
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

    let file_list = [];

    function handleUpload() {
        getRemittanceBatchPaymentList();
    }

    function getRemittanceBatchPaymentList() {
        jspa("/Invoice/NDIA/Remittance", "getRemittanceBatchPaymentList", {})
            .then((result) => {
                // toastSuccess("Payment Remittance Uploaded")
                file_list = result.result;
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

    getRemittanceBatchPaymentList();

    function generateInvoicesAndBatchPayments() {
        SpinnerStore.set({ show: true });
        jspa("/Invoice/NDIA/Remittance", "generateInvoicesAndBatchPayments", {})
            .then((result) => {
                toastSuccess("Invoices and Batch Payments have been generated");
                // file_list=result.result
                file_list = [];
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
</script>

<div class="px-2 mb-2">
    <h2
        class="text-xl sm:text-2xl font-fredoka-one-regular sm:tracking-tight"
        style="color:#220055;"
    >
        NDIA Remittance
    </h2>
    <!-- <p>Create invoices and payment batch records in Xero using NDIA Remittance files. These records can then be used to match and reconcile the payments received from NDIA.</p> -->
</div>

<NDIACSVImporter on:uploaded={handleUpload} />
<!-- 
        <ReportPanel on:click={()=>push('/invoicing/ndia/remittance/upload')} label="Upload a remittance file">
            Upload remittance data that will be used in the remittance report.
        </ReportPanel>

        <ReportPanel on:click={()=>push('/invoicing/ndia/remittance/summary')} label="Remittance Summary">
            Provides a summary report of what we invoiced and what we were paid.
        </ReportPanel>

       -->

<ul role="list" class=" bg-white rounded rounded-lg">
    {#each file_list as file, index (file.remittance_csv_name)}
        {#if (file_list[index - 1] && file_list[index - 1].claim_date !== file.claim_date) || index == 0}
            <li
                in:slide|global={{ duration: 150, delay: index * 10 }}
                class="relative pt-1 px-5 text-gray-700 border-t-gray-100 border-t-2"
            >
                <span class="text-xs font-bold"
                    >{formatDate(file.claim_date)}</span
                >
            </li>
        {/if}

        <li
            in:slide|global={{ duration: 150, delay: index * 10 }}
            class="relative flex justify-between border-t-gray-50 border-t py-1 px-5 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 {file.error
                ? 'text-red-800'
                : ''}"
        >
            <a
                href="/#/accounts/invoiced/ndia/remittance/{file.remittance_csv_name}"
                class="text-sm italic"
                >{file.remittance_csv_name}
            </a>
            <span class="text-sm"
                >{new Intl.NumberFormat("en-AU", {
                    style: "currency",
                    currency: "AUD",
                }).format(file.amount_paid)}</span
            >
        </li>
    {/each}
</ul>

{#if file_list.length > 0}
    <div class="p-4 flex justify-end">
        <button
            on:click={() => generateInvoicesAndBatchPayments()}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Create Invoices / Batch Payments in Xero</button
        >
    </div>
{/if}
