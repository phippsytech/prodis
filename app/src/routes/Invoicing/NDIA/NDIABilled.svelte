<script>
    import { push } from "svelte-spa-router";
    import Spinner from "@shared/PhippsyTech/svelte-ui/Spinner.svelte";
    import { jspa } from "@shared/jspa.js";
    import Roadblock from "@shared/appsec/Roadblock.svelte";
    import { checkXeroNDIAContactRoadblock } from "@shared/roadblocks/Xero/CheckNDIAContact.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";

    let roadblockChecks = [checkXeroNDIAContactRoadblock];

    let invoice_batches = [];
    let busy = false;

    BreadcrumbStore.set({
        path: [
            { url: "/invoicing", name: "Invoicing" },
            { url: null, name: "NDIA" },
        ],
    });

    jspa("/Invoice", "listNDIAInvoiceBatches", {}).then((result) => {
        invoice_batches = result.result;
    });

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

    function generateNDIAInvoicesInXero(invoice_batch) {
        busy = true;
        jspa("/Invoice", "generateNDIAInvoicesInXero", {
            invoice_batch: invoice_batch,
        })
            .then((result) => {
                // invoice_batches = result.result;
            })
            .finally(() => {
                busy = false;
            });
    }
</script>

<Roadblock {roadblockChecks}>
    {#if busy}
        <div class="text-center">
            <Spinner />
            <p class="text-gray-700 uppercase mb-2">
                Generating Invoices in Xero
            </p>
            <p>This can take a moment...</p>
        </div>
    {:else if invoice_batches.length}
        <table class="min-w-full">
            <thead class="border-b">
                <tr>
                    <th class="text-left">Batch</th>
                    <!-- <th class="text-left">Date</th> -->
                    <th class="text-left">CSV</th>
                </tr>
            </thead>
            {#each invoice_batches as item, index}
                <tr class="border-b hover:bg-blue-400">
                    <td class="px-2 py-2"
                        ><span class="font-bold">#{item.id}</span><br /><span
                            class="text-sm">{item.generation_date}</span
                        ></td
                    >
                    <td class="px-2 py-2">
                        <button
                            on:click={() => downloadNDIABulkCSV(item.id)}
                            type="button"
                            class="inline-block px-2 py-2 border-2 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded hover:bg-blue-400 focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
                            >Download</button
                        >
                        <button
                            on:click={() =>
                                push("/invoicing/ndia/billed/" + item.id)}
                            type="button"
                            class="inline-block px-2 py-2 border-2 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded hover:bg-blue-400 focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
                            >View</button
                        >
                    </td>
                </tr>
            {/each}
        </table>
    {:else}
        No NDIA invoice batches could be found
    {/if}
</Roadblock>
