<script>
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

    let clients = [];
    let reportList = [];

    BreadcrumbStore.set({
        path: [
            { url: "/reports", name: "Reports" },
            { url: null, name: "Overdue Visits" },
        ],
    });

    jspa("/Report", "getOverdueVisits", {}).then((result) => {
        clients = result.result;
        clients.forEach((client) => {
            // reportList.push({
            //     client_id:client.client_id,
            //     report_type:client.report_type[0].toUpperCase() + client.report_type.slice(1),
            //     name: client.name,
            //     date: client[client.report_type],
            //     therapist: client.therapist
            // })
            // reportList = reportList;
        });
        // reportList.sort(function(a, b) {
        //     return new Date(a.date) - new Date(b.date);
        // });
    });
</script>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2 mx-2">
    Primary Therapists / Clients without a session in the last 14 days
</h1>

<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each clients as client, index}
        <li
            in:slide={{ duration: 200 }}
            out:slide={{ duration: 200 }}
            class="px-6 py-2 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {clients.length -
                1 ==
            index
                ? 'rounded-b-lg'
                : ''}border-b border-gray-200 w-full {client.archived == 1
                ? 'text-gray-400 cursor-default'
                : ''}"
        >
            <!-- <div>{Math.abs(client.days)} days ago</div> -->
            <a
                href="/#/clients/{client.staff_id}"
                class="text-blue-600 hover:text-blue-700 hover:underline"
                >{client.therapist}</a
            >
            last saw
            <a
                href="/#/clients/{client.client_id}"
                class="text-blue-600 hover:text-blue-700 hover:underline"
                >{client.client_name}</a
            >
            {Math.abs(client.days_ago)} days ago ({@html convertMinutesToHoursAndMinutes(
                Math.round(client.budget) - client.total_session_minutes,
            )} available)
            <!-- <div class="text-xs">{client.therapist}</div> -->
            {#if client.client_on_hold == true}
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
            Yay! No overdue visits!
        </li>
    {/each}
</ul>
