<script>
    import { onMount } from "svelte";
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { slide } from "svelte/transition";
    import { getClient } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatDate, formatCurrency } from "@shared/utilities.js";

    export let params;

    let client_id = params.client_id;
    let invoices = [];
    // let invoice_summary = [];
    let client = {};

    onMount(async () => {
        client = await getClient(client_id);
        BreadcrumbStore.set({
            path: [
                { url: "/clients", name: "Clients" },
                { url: "/clients/" + client_id, name: client.name },
            ],
        });
    });

    jspa("/Invoice", "listClientInvoices", { client_id: client_id }).then(
        (result) => {
            invoices = result.result;
        },
    );

    // jspa("/Invoice", "getClientInvoiceSummary", { client_id: client_id }).then(
    //     (result) => {
    //         invoice_summary = result.result;
    //     },
    // );

    function buildInvoiceNumber(ndis_number, planmanager_id, invoice_batch) {
        let invoice_number =
            ndis_number + "-" + planmanager_id + "-" + invoice_batch;
        return invoice_number;
    }
</script>

<h2 class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular">
    Invoices
</h2>

<!-- {#each invoice_summary as item, index}
    <div>{item.code}: {formatCurrency(item.total)}</div>
{/each} -->

<!-- Header -->
<div class="hidden sm:block">
    <div
        class="grid grid-cols-2 border-b border-gray-200 items-center py-1 text-xs font-medium text-gray-500"
        style="grid-template-columns: 6fr 1fr;"
    >
        <div class="grid grid-cols-3 gap-4 items-center">
            <div>Invoice Number</div>
            <div>Date</div>
            <div>Plan Manager</div>
        </div>
        <div class="text-right">Total</div>
    </div>
</div>

<!-- Invoices -->
{#key invoices}
    {#each invoices as item, index}
        <div
            on:click={() =>
                push(
                    "/accounts/invoice/" +
                        buildInvoiceNumber(
                            client.ndis_number,
                            item.PlanManagerId,
                            item.InvoiceBatch,
                        ),
                )}
            in:slide|global={{ duration: 150 }}
            class="grid grid-cols-2 border-b border-gray-200 items-center hover:bg-indigo-50 py-1 cursor-pointer"
            style="grid-template-columns: 6fr 1fr;"
        >
            <div
                class="grid grid-cols-1 gap-0 sm:gap-4 sm:grid-cols-3 w-full items-center"
            >
                <div class="text-xs whitespace-no-wrap">
                    {buildInvoiceNumber(
                        client.ndis_number,
                        item.PlanManagerId,
                        item.InvoiceBatch,
                    )}
                </div>
                <div class="font-medium">{formatDate(item.GenerationDate)}</div>
                <div class="text-sm">{item.PlanManagerName}</div>
            </div>
            <div class="text-right">{formatCurrency(item.GrandTotal)}</div>
        </div>
    {/each}
{/key}
