<script>
    import { push } from "@app/../node_modules/svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { getDaysUntilDate, formatDate } from "@shared/utilities.js";
    import { StafferStore } from "@shared/stores.js";

    let notifications = [];
    let clients = [];
    let overdue_visits = [];

    $: {
        if ($StafferStore.id != null) {
            // staff_id = $StafferStore.id;
            getDueReportList($StafferStore.id);
            getOverdueVisits($StafferStore.id);
        }
    }

    function getDueReportList(staff_id) {
        notifications = [];
        jspa("/Report", "getDueReportList", { therapist_id: staff_id }).then(
            (result) => {
                clients = result.result;
                clients.forEach((client) => {
                    if (client.report_type) {
                        notifications.push({
                            client_id: client.id,
                            report_type:
                                client.report_type[0].toUpperCase() +
                                client.report_type.slice(1),
                            name: client.name,
                            date: client[client.report_type],
                            // therapist: client.therapist,
                            therapists: client.therapists,
                        });

                        notifications = notifications;
                    }
                });
                notifications.sort(function (a, b) {
                    return new Date(a.date) - new Date(b.date);
                });
            },
        );
    }

    function getOverdueVisits(staff_id) {
        jspa("/Report", "getOverdueVisits", { staff_id: staff_id }).then(
            (result) => {
                overdue_visits = result.result;
            },
        );
    }
</script>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2 mx-2">
    Items due in the next 21 days
</h1>

<ul
    class="bg-white rounded-lg border border-gray-200 w-full text-gray-900 mb-4"
>
    {#each notifications as notification, index}
        <li
            in:slide={{ duration: 200 }}
            out:slide|local={{ duration: 200 }}
            on:click={() => push("/notifications/" + notification.id)}
            class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {notifications.length -
                1 ==
            index
                ? 'rounded-b-lg'
                : ''}border-b border-gray-200 w-full"
        >
            <!-- {@html notification.text} -->

            <div class="font-bold">
                Due {formatDate(notification.date)}
                <span class="font-normal italic"
                    >({getDaysUntilDate(notification.date)})</span
                >
            </div>
            {notification.report_type} Report for
            <a
                href="/#/clients/{notification.client_id}"
                class="text-blue-600 hover:text-blue-700 hover:underline"
                >{notification.name}</a
            >
            {#if notification.therapists.length > 1}
                {#each notification.therapists as therapist}
                    <div class="text-xs">
                        {therapist.therapist_type} Therapist: {therapist.therapist}
                    </div>
                {/each}
            {/if}
        </li>
    {:else}
        <li
            class="px-6 py-2 border-b border-gray-200 w-full rounded-t-lg rounded-b-lg text-gray-400 cursor-default"
        >
            No Notifications
        </li>
    {/each}
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2 mx-2">
    Clients not seen in the last 14 days
</h1>

<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each overdue_visits as client, index}
        <li
            in:slide={{ duration: 200 }}
            out:slide|local={{ duration: 200 }}
            class="px-6 py-2 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {overdue_visits.length -
                1 ==
            index
                ? 'rounded-b-lg'
                : ''}border-b border-gray-200 w-full {client.archived == 1
                ? 'text-gray-400 cursor-default'
                : ''}"
        >
            <!-- <div>{Math.abs(client.days)} days ago</div> -->

            <a
                href="/#/clients/{client.client_id}"
                class="text-blue-600 hover:text-blue-700 hover:underline"
                >{client.client_name}</a
            >
            {Math.abs(client.days_ago)} days ago
            <span class="text-xs"
                >( {Math.round(client.budget) - client.total_session_minutes} minutes
                available)</span
            >
        </li>
    {:else}
        <li
            class="px-6 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default"
        >
            Yay! No overdue visits!
        </li>
    {/each}
</ul>
