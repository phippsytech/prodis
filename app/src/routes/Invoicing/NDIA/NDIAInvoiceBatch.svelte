<script>
    import LineItems from "../LineItems.svelte";
    import { jspa } from "@shared/jspa.js";
    import Roadblock from "@shared/appsec/Roadblock.svelte";
    import { checkXeroNDIAContactRoadblock } from "@shared/roadblocks/Xero/CheckNDIAContact.svelte";

    import { BreadcrumbStore } from "@shared/stores.js";

    let roadblockChecks = [checkXeroNDIAContactRoadblock];

    export let params;

    let invoice_batch = params.invoice_batch;
    let line_items = [];

    let total = 0;

    BreadcrumbStore.set({
        path: [
            { url: "/invoicing", name: "Invoicing" },
            { url: "/invoicing/ndia", name: "NDIA" },
            { url: null, name: "Invoice Batch " + invoice_batch },
        ],
    });

    jspa("/Invoice", "listInvoiceAggregatedLineItems", {
        invoice_batch: invoice_batch,
        type: "NDIA",
    }).then((result) => {
        line_items = result.result;

        line_items.forEach((item) => {
            total = total + item.Quantity * item.UnitPrice;
        });
    });

    // function generateNDIAInvoicesInXero(invoice_batch){
    //     busy= true;
    //     jspa("/Invoice", "generateNDIAInvoicesInXero", {invoice_batch: invoice_batch}).then((result)=>{
    //         // invoice_batches = result.result;
    //     })
    //     .finally(()=>{
    //         busy= false;
    //     });
    // }

    function downloadNDIABulkCSV(invoice_batch) {
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
            .catch((result) => {});
    }
</script>

<Roadblock {roadblockChecks}>
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2
                class="text-xl sm:text-2xl font-fredoka-one-regular sm:tracking-tight"
                style="color:#220055;"
            >
                NDIA Invoice Batch {invoice_batch}
            </h2>

            {#if line_items.length}
                <p class="">
                    The total for this invoice batch is {total.toLocaleString(
                        "en-AU",
                        { style: "currency", currency: "AUD" },
                    )}.
                    <!-- When you are ready you can generate a CSV to send to NDIA using the button at the bottom of the table.  This action creates an invoice batch and marks the sessions billed. -->
                </p>
            {/if}
        </div>
        <button
            on:click={() => downloadNDIABulkCSV(invoice_batch)}
            type="button"
            class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
            >Download CSV</button
        >
    </div>

    <LineItems {line_items} mode="reverse" />
</Roadblock>
