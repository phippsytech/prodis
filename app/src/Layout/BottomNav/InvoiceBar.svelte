<script>
    import { slide } from "svelte/transition";
    import { onMount } from "svelte";
    import { InvoiceBarStore } from "./stores.js";

    $: invoice_bar = $InvoiceBarStore;

    function generateInvoices() {
        invoice_bar.generateInvoices();
    }

    onMount(async () => {
        window.addEventListener("popstate", () => (invoice_bar.show = false));
    });

    // onMount(async () => {
    //     window.addEventListener('popstate', function(event) {
    //         invoice_bar.show = false;
    //     });
    // });
</script>

{#if invoice_bar.show}
    <div transition:slide={{ duration: 150 }} class="h-16 block"></div>
    <!-- <div class="p-10 lg:p-5"></div> -->
    <div class="fixed bottom-0 z-20 w-full block">
        <div
            transition:slide={{ duration: 150 }}
            class="  bg-white border-t-2 border-indigo-100"
            style="width:-webkit-fill-available"
        >
            <div class="mx-auto max-w-7xl px-2">
                <div class="flex justify-between p-2 items-center">
                    {#if invoice_bar.selected_total > 0}
                        <div>
                            <div class="text-sm font-bold text-slate-400">
                                Amount to be invoiced:
                            </div>
                            <p
                                class="text-2xl font-bold leading-7 text-slate-900 sm:tracking-tight"
                            >
                                {invoice_bar.selected_total.toLocaleString(
                                    "en-AU",
                                    { style: "currency", currency: "AUD" },
                                )}
                            </p>
                        </div>
                    {:else}
                        <div>
                            <div class="text-sm opacity-50">
                                <span class="font-bold"
                                    >No billables selected.</span
                                > &nbsp;You need to select billables you would like
                                to invoice before you can generate invoices.
                            </div>
                        </div>
                    {/if}

                    <button
                        type="button"
                        class="justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 {invoice_bar.selected_total >
                        0
                            ? ''
                            : 'opacity-50 cursor-not-allowed'}"
                        on:click={invoice_bar.selected_total > 0
                            ? () => generateInvoices()
                            : null}
                        disabled={invoice_bar.selected_total <= 0
                            ? true
                            : false}>Generate Invoices</button
                    >
                </div>
            </div>
        </div>
    </div>
{/if}
