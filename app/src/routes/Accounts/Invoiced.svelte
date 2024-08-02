<script>
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatDate } from "@shared/utilities.js";

    let invoice_batches = [];

    BreadcrumbStore.set({
        path: [{ url: "/accounts", name: "Accounts" }],
    });

    jspa("/Invoice", "listInvoiceBatches", {}).then((result) => {
        invoice_batches = result.result;
    });
</script>

{#if invoice_batches.length}
    <p class="m-2 mb-4">
        Here is a summary of invoice batches and dates that Invoices have been
        generated for.
    </p>

    {#each invoice_batches as item, index}
        <!-- <div on:click={()=>push("/invoicing/managed/billed/"+item.id)} class="border-b flex justify-between items-center hover:bg-blue-600 hover:text-white group cursor-pointer"> -->
        <div
            role="button"
            tabindex="0"
            on:click={() => push("/accounts/invoiced/" + item.id)}
            on:keypress={(event) => {
                if (event.key === "Enter")
                    push("/accounts/invoiced/" + item.id);
            }}
            class="border-b flex justify-between items-center hover:bg-indigo-50 hover:text-indigo-600 group cursor-pointer"
        >
            <span class="px-2 py-2">
                <span class="px-2 py-2 font-bold">#{item.id}</span>

                <span class="px-2 py-2">{formatDate(item.generation_date)}</span
                >
            </span>
        </div>
    {/each}
{:else}
    No Managed invoice batches could be found
{/if}
