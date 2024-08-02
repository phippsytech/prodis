<script>
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import LineItems from "../LineItems.svelte";
    import Spinner from "@shared/PhippsyTech/svelte-ui/Spinner.svelte";
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { getMonday, getDatePlus7Days } from "@shared/utilities.js";

    const start_year = new Date().getFullYear() - 1;
    const end_year = new Date().getFullYear() + 1;

    let start_date = getMonday();
    let end_date = getDatePlus7Days(start_date);
    let unbilled_total = 0;
    let managed = [];
    let generating_invoices = false;

    BreadcrumbStore.set({
        path: [
            { url: "/invoicing", name: "Invoicing" },
            { url: null, name: "Managed" },
        ],
    });

    jspa("/Invoice", "listUnbilledManaged", {}).then((result) => {
        managed = result.result;
        unbilled_total = 0;
        managed.forEach((item) => {
            unbilled_total = unbilled_total + item.Quantity * item.UnitPrice;
        });
    });

    function generateManagedInvoiceBatch() {
        generating_invoices = true;
        jspa("/Invoice", "generateManagedInvoiceBatch", {
            start_date: start_date,
            end_date: end_date,
        })
            .then((result) => {
                generating_invoices = false;
                push("/invoicing/managed/billed/");
            })
            .catch((result) => {
                generating_invoices = false;
            });
    }
</script>

{#if managed.length}
    {#if !generating_invoices}
        <p class="mb-4">
            Your current unbilled Managed clients total is {unbilled_total.toLocaleString(
                "en-AU",
                { style: "currency", currency: "AUD" },
            )}.
        </p>

        <div class="text-sm mb-1 font-bold opacity-50">Billing Period</div>

        <div class="flex flex-wrap space-x-2 items-center md:flex-no-wrap">
            <FloatingDate label="Start Date" bind:value={start_date} />
            <FloatingDate label="End Date" bind:value={end_date} />

            <button
                on:click={() => generateManagedInvoiceBatch()}
                type="button"
                class="block justify-center rounded-md bg-indigo-600 px-3 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
                >Generate Managed Invoices
            </button>
        </div>

        <LineItems line_items={managed} />
    {:else}
        <div class="flex items-center justify-center p-4" style="height:100vh">
            <div class="w-full" style="max-width:400px;">
                <div class="text-center">
                    <Spinner />
                    <p class="text-gray-700 uppercase mb-2">
                        Generating Invoices
                    </p>
                    <p>This can take a minute or so...</p>
                </div>
            </div>
        </div>
    {/if}
{:else}
    <p>There are no unbilled managed clients.</p>
{/if}
