<script>
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import LineItems from "./LineItems.svelte";
    import GroupedLineItems from "./GroupedLineItems.svelte";
    import Spinner from "@shared/PhippsyTech/svelte-ui/Spinner.svelte";
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { getMonday, getDatePlus7Days, decimalRounder, getQueryParams } from "@shared/utilities.js";
    import { InvoiceBarStore } from "@app/Layout/BottomNav/stores.js";
    import QueryManager from "@shared/QueryManager.svelte";
    import Filter from "@shared/PhippsyTech/svelte-ui/Filter.svelte";
    import { consoleLogs } from "@app/Overlays/stores";
  import { onMount } from "svelte";
    


    let queryParams = getQueryParams();
    let search = queryParams.search;
    let filter = queryParams.filter;
 
    $: queryParams = { search, filter };

    let filters = [
		{ label: "name", enabled: false }
	];

    let start_date = getMonday();
    let end_date = getDatePlus7Days(start_date);
    let unbilled_total = 0;
    let selected_total = 0;
    let managed = [];
    let generating_invoices = false;
  
    let filteredManaged = [];

    let selectedLineItems = [];
    let lineItemElement;


   

    BreadcrumbStore.set({
        path: [{ url: null, name: "Accounts" }],
    });

    onMount(() => {
        jspa("/Invoice", "listUnbilled", {}).then((result) => {
            managed = result.result;
            unbilled_total = 0;
            managed.forEach((item) => {
                //unbilled_total = unbilled_total + item.Quantity * item.UnitPrice;
                let itemTotal = decimalRounder(item.Quantity * item.UnitPrice);
                unbilled_total = decimalRounder(unbilled_total + itemTotal);
            });
            console.log(managed);
            managed.sort((a, b) => {
                if (a.ClientName === b.ClientName) {
                    return a.PlanManagerId > b.PlanManagerId ? 1 : -1;
                }
                return a.ClientName > b.ClientName ? 1 : -1;
            });
        });
    })

    

    function generateInvoices() {
        generating_invoices = true;
        jspa("/Invoice", "generateInvoiceBatch", {
            session_ids: selectedLineItems,
        })
            .then((result) => {
                let invoice_batch = result.result.invoice_batch;
                generating_invoices = false;
                push("/accounts/invoiced/" + invoice_batch);
            })
            .catch((result) => {
                generating_invoices = false;
            });
    }

    $: {
        if (lineItemElement && lineItemElement.handleSelectByDate) {
            lineItemElement.handleSelectByDate(start_date, end_date);
        }
    }

    $: {
        InvoiceBarStore.set({
            selected_total: selected_total,
            show: true,
            generateInvoices: () => generateInvoices(),
        });
    }

    $: {
        if (filters.find((f) => f.label === "name").enabled) {
            filter = 'name';
        }
    }

    $: {
        if (search) {
            if (filter && search) {
            
                managed = managed.filter( (item)  => item.ClientName &&
                            item.ClientName.toLowerCase().includes(search.toLowerCase())); 

            }  else {
                managed = managed;
             }
        } else if (!filter && !search) {
            managed = managed;
        }
      

	}
</script>

<QueryManager
    params={{ ...queryParams }}
/>

<div class="flex h-16 shrink-0 items-center bg-white rounded-md">
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-2 px-3">
        <div class="relative flex flex-1">
            <label for="search-field" class="sr-only">Search</label>
            <svg
                class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                    clip-rule="evenodd"
                />
            </svg>
            <input
                bind:value={search}
                id="search-field"
                class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                placeholder="search by staff name..."
                type="search"
                name="search"
            />
        </div>

    </div>
</div>
<div class="bg-white px-3 rounded-md pb-1">
    <Filter bind:filters />
</div>

{#if managed.length}
    {#if !generating_invoices}
        <div class="bg-slate-100 px-3 pt-2 pb-4 mb-2 rounded-md">
            <h1 class="text-indigo-900 text-lg font-bold mt-0 mb-2">
                Select billables to invoice
            </h1>

            <div class="text-sm mb-1 text-slate-400">Billing Period</div>

            <div class="flex flex-wrap space-x-2 items-center md:flex-no-wrap">
                <FloatingDate label="Start Date" bind:value={start_date} />
                <FloatingDate label="End Date" bind:value={end_date} />
            </div>
        </div>

        <!-- <LineItems
            bind:this={lineItemElement}
            line_items={managed}
            bind:selected_total
            bind:selectedLineItems
        /> -->

        <GroupedLineItems
            bind:this={lineItemElement}
            line_items={managed}
            bind:selected_total
            bind:selectedLineItems
        />
    {:else}
        <div class="flex items-center justify-center p-4" style="height:100vh">
            <div class="w-full" style="max-width:400px;">
                <div class="text-center">
                    <Spinner />
                    <p class="text-gray-700 uppercase mb-2">
                        Generating Invoices for selected items
                    </p>
                    <p>This can take a minute or so...</p>
                </div>
            </div>
        </div>
    {/if}
{:else}
    <p>There are no unbilled participants.</p>
{/if}
