<script>
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { slide } from "svelte/transition";
    import Roadblock from "@shared/appsec/Roadblock.svelte";
    import { checkXeroNDIAContactRoadblock } from "@shared/roadblocks/Xero/CheckNDIAContact.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";

    let roadblockChecks = [checkXeroNDIAContactRoadblock];

    export let params;

    let invoice_batch = params.invoice_batch;
    let line_items = [];

    BreadcrumbStore.set({
        path: [
            { url: "/invoicing", name: "Invoicing" },
            { url: "/invoicing/managed", name: "Managed" },
        ],
    });

    let total = 0;

    jspa("/Invoice", "listInvoiceAggregatedLineItems", {
        // invoice_batch: invoice_batch, type:"Managed"
        invoice_batch: invoice_batch,
    }).then((result) => {
        line_items = result.result;
        line_items.forEach((item) => {
            total = total + item.Quantity * item.UnitPrice;
        });
    });

    function getTotalValueByNDISNumberAndPlanManager(
        ndisNumber,
        planManagerId,
    ) {
        const totalValue = line_items.reduce((total, item) => {
            if (
                item.NDISNumber === ndisNumber &&
                item.PlanManagerId === planManagerId
            ) {
                return (
                    total +
                    parseFloat(item.Quantity) * parseFloat(item.UnitPrice)
                );
            }
            return total;
        }, 0);
        return totalValue.toFixed(2);
    }
</script>

<Roadblock {roadblockChecks}>
    <h2
        class="text-xl sm:text-2xl font-fredoka-one-regular"
        style="color:#220055;"
    >
        Managed Invoice Batch {invoice_batch}
    </h2>

    {#if line_items.length}
        <p class="mb-4">
            The total for this invoice batch is {total.toLocaleString("en-AU", {
                style: "currency",
                currency: "AUD",
            })}.
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
                        push(
                            "/invoicing/invoice/" +
                                item.NDISNumber +
                                "-" +
                                item.PlanManagerId +
                                "-" +
                                item.InvoiceBatch,
                        )}
                    in:slide|global={{ duration: 150 }}
                    class="grid grid-cols-2 border-b border-gray-200 items-center hover:bg-indigo-50 py-1 cursor-pointer"
                    style="grid-template-columns: 6fr 1fr;"
                >
                    <div
                        class="grid grid-cols-1 gap-0 sm:gap-4 sm:grid-cols-3 w-full items-center"
                    >
                        <div class="font-medium">{item.ClientName}</div>
                        <div class="text-xs whitespace-no-wrap">
                            {item.NDISNumber}-{item.PlanManagerId}-{item.InvoiceBatch}
                        </div>
                        <div class="text-sm">{item.ClientBillingName}</div>
                    </div>
                    <div class="text-right">
                        ${getTotalValueByNDISNumberAndPlanManager(
                            item.NDISNumber,
                            item.PlanManagerId,
                        )}
                    </div>
                </div>
            {/if}
        {/each}
    {/key}
</Roadblock>
