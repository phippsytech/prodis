<script>
    import { push } from "svelte-spa-router";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import LineItems from "../LineItems.svelte";
    import { jspa } from "@shared/jspa.js";
    import Roadblock from "@shared/appsec/Roadblock.svelte";
    import { checkXeroNDIAContactRoadblock } from "@shared/roadblocks/Xero/CheckNDIAContact.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { getMonday, getDatePlus7Days } from "@shared/utilities.js";

    const date = new Date();

    let start_date = getMonday();
    let end_date = getDatePlus7Days(start_date);

    let roadblockChecks = [checkXeroNDIAContactRoadblock];
    let ndia = [];
    let unbilled_total = 0;

    BreadcrumbStore.set({
        path: [
            { url: "/invoicing", name: "Invoicing" },
            { url: null, name: "NDIA" },
        ],
    });

    jspa("/Invoice", "listUnbilledNDIA", {}).then((result) => {
        ndia = result.result;
        unbilled_total = 0;
        ndia.forEach((item) => {
            unbilled_total = unbilled_total + item.Quantity * item.UnitPrice;
        });
    });

    function generateNDIAInvoiceBatch() {
        jspa("/Invoice", "generateNDIAInvoiceBatch", {
            start_date: start_date,
            end_date: end_date,
        }).then((result) => {
            let invoice_batch = result.invoice_batch;
            ndia = []; // NDIA is now reset.

            push("/invoicing/ndia/billed/" + invoice_batch);
        });
    }
</script>

<Roadblock {roadblockChecks}>
    {#if ndia.length}
        <p class="mb-4">
            Your current unbilled NDIA clients total is {unbilled_total.toLocaleString(
                "en-AU",
                { style: "currency", currency: "AUD" },
            )}.
            <!-- When you are ready you can generate a CSV to send to NDIA using the button at the bottom of the table.  This action creates an invoice batch and marks the sessions billed. -->
        </p>

        <LineItems line_items={ndia} />

        <div class="text-sm mb-1 font-bold opacity-50">Billing Period</div>

        <div
            class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
        >
            <div class="flex-grow">
                <FloatingDate label="Start Date" bind:value={start_date} />
            </div>
            <div class="flex-grow">
                <FloatingDate label="End Date" bind:value={end_date} />
            </div>
            <button
                on:click={() => generateNDIAInvoiceBatch()}
                type="button"
                class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
                >Generate NDIA CSV
            </button>
        </div>
    {:else}
        <p>There are no unbilled NDIA clients.</p>
    {/if}
</Roadblock>
