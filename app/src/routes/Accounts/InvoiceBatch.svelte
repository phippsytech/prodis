<script>
    import { onMount } from "svelte";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { slide } from "svelte/transition";
    import Roadblock from "@shared/appsec/Roadblock.svelte";
    import { checkXeroNDIAContactRoadblock } from "@shared/roadblocks/Xero/CheckNDIAContact.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatCurrency } from "@shared/utilities.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";

    let roadblockChecks = [checkXeroNDIAContactRoadblock];

    export let params;

    let invoice_batch = params.invoice_batch;
    let ndis_number = "";
    let line_items = [];
    let invoice_batch_errors = [];
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

    let total = 0;

    SpinnerStore.set({ show: true, message: "Loading Invoice Batch Data" });

    onMount(() => {
        jspa("/Invoice", "getInvoiceBatch", { invoice_batch: invoice_batch })
            .then((result) => {
                line_items = result.result;
                line_items.forEach((item) => {
                    total = total + parseFloat(item.InvoiceTotal);
                });
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    });

    function downloadNDIABulkCSV() {
        jspa("/Invoice", "getNDIABulkCSV", { invoice_batch: invoice_batch })
            .then((result) => {
                const blob = new Blob([result.result], { type: "text/csv" });
                // @ts-ignore
                if (window.navigator.msSaveOrOpenBlob) {
                    // @ts-ignore
                    window.navigator.msSaveBlob(
                        blob,
                        "ndia" + invoice_batch + ".csv",
                    );
                } else {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = "ndia" + invoice_batch + ".csv";
                    a.click();
                    URL.revokeObjectURL(url);
                }
            })
            .catch((result) => {
                toastError("ERROR: " + result.error_message);
            });
    }

    $: containsNDIAInvoices = line_items.some(
        (item) => item.PlanManagerId == 45,
    );
</script>

<Roadblock {roadblockChecks}>
    <div class="flex justify-between items-center">
        <h2
            class="text-xl sm:text-2xl font-fredoka-one-regular"
            style="color:#220055;"
        >
            Invoice Batch {invoice_batch}
        </h2>
        <!-- <button
            on:click={() => downloadNDIABulkCSV()}
            type="button"
            class="block justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
        ></button> -->

        <button
            type="button"
            class="justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 {containsNDIAInvoices
                ? ''
                : 'opacity-50 cursor-not-allowed'}"
            on:click={containsNDIAInvoices ? () => downloadNDIABulkCSV() : null}
            disabled={!containsNDIAInvoices}
            >{#if containsNDIAInvoices}Download NDIA CSV{:else}No NDIA CSV data{/if}</button
        >
    </div>

    {#if line_items.length}
        <p class="mb-4">
            The total for this invoice batch is {formatCurrency(total)}.
        </p>
    {/if}

    <!-- Header -->
    <div class="hidden sm:block">
        <div
            class="grid grid-cols-2 border-b border-gray-200 items-center py-1 text-xs font-medium text-gray-500"
            style="grid-template-columns: 6fr 1fr;"
        >
            <div class="grid grid-cols-3 gap-4 items-center">
                <div>Participant</div>
                <div>Invoice Number</div>
                <div>Plan Manager</div>
            </div>
            <div class="text-right">Total</div>
        </div>
    </div>

    <!-- Invoices -->
    {#key line_items}
        {#each line_items as item, index}
            {#if index == 0 || item.PlanManagerId != line_items[index - 1].PlanManagerId || item.ClientName != line_items[index - 1].ClientName}
                <div
                    on:click={() =>
                        push("/accounts/invoice/" + item.ClaimReference)}
                    in:slide|global={{ duration: 150 }}
                    class="grid grid-cols-2 border-b border-gray-200 items-center hover:bg-indigo-50 hover:text-indigo-600 py-1 cursor-pointer
                    
                    "
                    style="grid-template-columns: 6fr 1fr;"
                >
                    <div
                        class="grid grid-cols-1 gap-0 sm:gap-4 sm:grid-cols-3 w-full items-center"
                    >
                        <div class="font-medium">{item.ClientName}</div>
                        <div class="text-xs whitespace-no-wrap">
                            {item.ClaimReference}
                        </div>
                        <div class="text-sm">{item.PlanManagerName}</div>
                    </div>
                    <div class="text-right">
                        ${item.InvoiceTotal}
                    </div>
                </div>
            {/if}
        {/each}
    {/key}
</Roadblock>
