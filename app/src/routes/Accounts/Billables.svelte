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
    import ClientSelector from "@shared/ClientSelector.svelte";
    import Client from "@app/Layout/SideBar/Client.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";


    let queryParams = getQueryParams();
    let client_id = queryParams.client_id;
    let service_id = queryParams.service_id;
    $: queryParams = { client_id, service_id };


    let start_date = getMonday();
    let end_date = getDatePlus7Days(start_date);
    let unbilled_total = 0;
    let selected_total = 0;
    let managed = [];
    let clients;
    let services = [];
    let filteredManaged = [];
    let generating_invoices = false;

    let selectedLineItems = [];
    let lineItemElement;


    BreadcrumbStore.set({
        path: [{ url: null, name: "Accounts" }],
    });

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


    jspa("/Participant", "listClients", {}).then((result) => {
        clients = result.result
        .filter((item) => item.archived != 1) // Filter out archived staff
        .map((item) => ({
            label: `${item.client_name}`,
            value: item.client_id,
        }))
        .sort((a, b) => a.label.localeCompare(b.label));
    });


    jspa("/Service", "listServices", {}).then((result) => {
        console.log(result.result);
        services = result.result.filter((item) => item.archived != 1) 
        .map((item) => ({
            label: `${item.code}`,
            value: item.id,
        }))
        .sort((a, b) => a.label.localeCompare(b.label));
    });

    

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
        if (client_id) {
         
            filteredManaged = managed.filter( (item)  =>
                            item.ClientId == client_id);
         
        } else if (service_id)  {
            filteredManaged = managed.filter( (item)  =>
                item.ServiceId == service_id);
        }else {
            
            filteredManaged = managed;
        }
      
	}
</script>

<QueryManager
    params={{ ...queryParams }}
   />


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


        <!-- <div class="flex flex-wrap space-x-2 items-center md:flex-no-wrap">
            <ClientSelector bind:client_id={client_id} clearable />
         
            
        </div> -->


        <FloatingCombo
            label="Clients"
            items={clients}
            bind:value={client_id}
            placeholderText="Select or type name ..."
        />


        <FloatingCombo
            label="Services"
            items={services}
            bind:value={service_id}
            placeholderText="Select or type service code ..."
        />

        <!-- <LineItems
            bind:this={lineItemElement}
            line_items={managed}
            bind:selected_total
            bind:selectedLineItems
        /> -->

        <GroupedLineItems
            bind:this={lineItemElement}
            line_items={filteredManaged}
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
