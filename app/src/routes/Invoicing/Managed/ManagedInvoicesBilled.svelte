<script>
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatDate } from "@shared/utilities.js";

    let invoice_batches = [];

    BreadcrumbStore.set({
        path: [
            { url: "/invoicing", name: "Invoicing" },
            { url: null, name: "Managed" },
        ],
    });

    jspa("/Invoice", "listManagedInvoiceBatches", {}).then((result) => {
        invoice_batches = result.result;
    });
</script>

{#if invoice_batches.length}
    <p class="m-2 mb-4">
        Here is a summary of the invoice batches and dates that Managed Client
        Invoices have been generated for.
    </p>

    {#each invoice_batches as item, index}
        <!-- <div on:click={()=>push("/invoicing/managed/billed/"+item.id)} class="border-b flex justify-between items-center hover:bg-blue-600 hover:text-white group cursor-pointer"> -->
        <div
            role="button"
            tabindex="0"
            on:click={() => push("/invoicing/managed/billed/" + item.id)}
            on:keypress={(event) => {
                if (event.key === "Enter")
                    push("/invoicing/managed/billed/" + item.id);
            }}
            class="border-b flex justify-between items-center hover:bg-blue-600 hover:text-white group cursor-pointer"
        >
            <span class="px-2 py-2">
                <span class="px-2 py-2 font-bold">#{item.id}</span>
                <span class="px-2 py-2">{formatDate(item.generation_date)}</span
                >
            </span>
            <span>
                <button
                    on:click={() =>
                        push("/invoicing/managed/billed/" + item.id)}
                    type="button"
                    class="inline-flex justify-center rounded-md bg-blue-600 px-3 py-1 text-xs text-white hover:bg-blue-500 hidden group-hover:block"
                    >View</button
                >
            </span>
        </div>
    {/each}
{:else}
    No Managed invoice batches could be found
{/if}
