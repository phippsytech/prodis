<script>
    import LineItems from "./LineItems.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";
    import { formatDate } from "@shared/utilities.js";

    export let params;

    const invoice_number = params.invoice_number;

    let error = false;
    let ndis_number;

    let invoice_batch;
    let invoice_batch_date;
    let line_items = [];
    let total = 0;

    // let invoice_errors = [];

    let planmanager_name;
    let planmanager_id;
    let client_name;
    let client_id;

    // check invoice_id can be split into 2 or 3 parts.
    if (
        invoice_number.split("-").length < 2 ||
        invoice_number.split("-").length > 3
    ) {
        error = true;
    }

    if (!error) {
        ndis_number = invoice_number.split("-")[0];
        planmanager_id = invoice_number.split("-")[1];
        invoice_batch = invoice_number.split("-")[2];

        BreadcrumbStore.set({
            path: [
                {
                    url: "/accounts",
                    name: "Accounts",
                },
                {
                    url: "/accounts/invoiced/" + invoice_batch,
                    name: "Invoice Batch " + invoice_batch,
                },
            ],
        });

        jspa("/Invoice", "getInvoice", {
            invoice_batch: invoice_batch,
            ndis_number: ndis_number,
            planmanager_id: planmanager_id,
        }).then((result) => {
            line_items = result.result;

            line_items.forEach((item) => {
                item.Quantity = parseFloat(item.Quantity).toFixed(2);
                total = total + item.Quantity * item.UnitPrice;
                planmanager_name = item.ClientBillingName;
                client_name = item.ClientName;
                client_id = item.ClientId;
                planmanager_id = item.PlanManagerId;
            });
        });

        jspa("/Invoice", "getInvoiceBatchDate", {
            invoice_batch: invoice_batch,
        }).then((result) => {
            invoice_batch_date = result.result;
        });
    }

    function reverseInvoice() {
        jspa("/Invoice", "reverseInvoice", {
            invoice_number: invoice_number,
        }).then((result) => {
            push("/accounts/billables");
        });
    }
</script>

{#if error}
    <p>Invalid invoice number</p>
{:else}
    <div class="flex justify-between py-2 mb-4 gap-4 items-center">
        <div>
            <h2
                class="text-xl sm:text-2xl font-fredoka-one-regular"
                style="color:#220055;"
            >
                Invoice {invoice_number}
            </h2>
            <p class="text-xl">{formatDate(invoice_batch_date)}</p>

            {#if line_items.length}
                <p class="">
                    Invoice Total: {total.toLocaleString("en-AU", {
                        style: "currency",
                        currency: "AUD",
                    })}
                </p>
            {/if}
        </div>
        <!-- {#if planmanager_id != 45} -->
        <button
            on:click={() => reverseInvoice()}
            type="button"
            class="w-auto inline-flex justify-center rounded-md border border-indigo-600 px-3 py-2 text-sm text-indigo-600 font-medium hover:text-white hover:bg-indigo-600"
            >Reverse</button
        >
        <!-- {/if} -->
    </div>

    <div class="flex justify-start py-2 mb-4 gap-4">
        <div class="text-sm">
            <div class="text-xs font-medium text-gray-500">Participant</div>
            <a
                class="text-indigo-600 hover:underline"
                href="/#/clients/{client_id}">{client_name}</a
            >
        </div>
        <div class="text-sm">
            <div class="text-xs font-medium text-gray-500">Plan Manager</div>
            <a
                class="text-indigo-600 hover:underline"
                href="/#/planmanagers/{planmanager_id}">{planmanager_name}</a
            >
        </div>
    </div>

    <LineItems {line_items} />
{/if}
