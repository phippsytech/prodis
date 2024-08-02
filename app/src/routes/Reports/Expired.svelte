<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import {
        formatCurrency,
        getDaysUntilDate,
        formatDate,
        convertMinutesToHoursAndMinutes,
    } from "@shared/utilities.js";

    BreadcrumbStore.set({
        path: [
            { url: "/reports", name: "Reports" },
            { url: null, name: "Expired NDIS Clients" },
        ],
    });

    let clients = [];

    onMount(async () => {
        jspa("/Report", "getExpiredNDISClients", {}).then((result) => {
            clients = result.result;
            clients.sort(function (a, b) {
                return (
                    new Date(a.ndis_plan_end_date) -
                    new Date(b.ndis_plan_end_date)
                );
            });
        });
    });
</script>

<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each clients as client, index (index)}
        <li
            in:slide={{ duration: 200 }}
            out:slide={{ duration: 200 }}
            class="px-6 py-2 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {client.length -
                1 ==
            index
                ? 'rounded-b-lg'
                : ''}border-b border-gray-200 w-full {client.archived == 1
                ? 'text-gray-400 cursor-default'
                : ''}"
        >
            <a class="text-blue-600" href="/#/clients/{client.id}"
                >{client.name}</a
            >
            - {formatDate(client.ndis_plan_end_date)} ({getDaysUntilDate(
                client.ndis_plan_end_date,
            )})

            {#if client.on_hold == true}
                <span
                    class="text-xs bg-red-600 text-white px-2 py-1 rounded-full ml-2"
                    >On Hold</span
                >
            {/if}
        </li>
    {:else}
        <li
            class="px-6 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default"
        >
            No expired NDIS Plans found.
        </li>
    {/each}
</ul>
