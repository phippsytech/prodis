<script>
    import Container from "@shared/Container.svelte";
    import { PencilIcon } from "heroicons-svelte/24/outline";
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
    import { push } from "svelte-spa-router";
    import { onMount, onDestroy } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { getClient } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import {
        formatDate,
        convertMinutesToHoursAndMinutes,
    } from "@shared/utilities.js";

    export let client_id;
    export let billed = false;

    const date = new Date();
    const start_year = date.getFullYear() - 1;
    const end_year = date.getFullYear() + 1;

    // let client_id = client.id
    let client = {};
    let timetracking = [];
    let timetrackingList = [];
    let service_codes = [];
    let selected_service_codes = [];
    // let filter = {
    //     start_date: "2023-06-19",
    //     end_date: "2023-06-25",
    // };

    let claim_types = [
        { option: "Direct Service (In-Person with Participant)", value: "" },
        { option: "Cancellation", value: "CANC" },
        { option: "NDIA Required Report", value: "REPW" },
        { option: "Provider Travel", value: "TRAN" },
        {
            option: "Non-Face-to-Face Services (Did not see the participant)",
            value: "NF2F",
        },
    ];

    function getOption(value) {
        if (value === null) value = "";
        let option = "";
        claim_types.forEach((item) => {
            if (item.value == value) {
                option = item.option;
            }
        });
        if (value == null && billed == false) {
            option = "<span class='text-red-400'>NOT SET</span>";
        }
        return option;
    }

    onMount(async () => {
        client = await getClient(client_id);

        BreadcrumbStore.set({
            path: [
                { url: "/clients", name: "Clients" },
                { url: "/clients/" + client_id, name: client.name },
            ],
        });

        jspa("/TimeTracking", "listTimeTrackingByClientId", {
            client_id: client_id,
            billed: billed,
        })
            .then((result) => {
                timetracking = result.result;

                // Make a distinct list of service codes to use as a filter.
                service_codes = [
                    ...new Set(timetracking.map((item) => item.service_code)),
                ];

                filterTimeTrackings();
            })
            .catch((error) => (timetracking = []));
    });

    function toggleServiceCode(service_code) {
        if (selected_service_codes.includes(service_code)) {
            selected_service_codes = selected_service_codes.filter(
                (code) => code !== service_code,
            );
        } else {
            selected_service_codes = [...selected_service_codes, service_code];
        }
        filterTimeTrackings();
    }

    function filterTimeTrackings() {
        timetrackingList = JSON.parse(JSON.stringify(timetracking));
        return;

        // Filter by date
        if (filter.start_date && filter.end_date) {
            timetrackingList = timetrackingList.filter((item) => {
                let item_date = new Date(item.session_date);
                let start_date = new Date(filter.start_date);
                let end_date = new Date(filter.end_date);
                return item_date >= start_date && item_date <= end_date;
            });
        }

        // Filter by service code
        if (selected_service_codes.length > 0) {
            timetrackingList = timetrackingList.filter((item) =>
                selected_service_codes.includes(item.service_code),
            );
        }
    }
</script>

<table class="min-w-full">
    <thead>
        <tr class="border-b">
            <th class="text-left text-xs px-2">Date</th>
            <th class="text-left text-xs px-2">Support Item</th>
            <th class="text-left text-xs px-2">Staffer</th>
            <th class="text-left text-xs px-2">Plan Manager</th>
            <th class="text-xs px-2">Claim Type</th>
            <th class="text-xs px-2">Duration</th>

            <th></th>
        </tr>
    </thead>
    {#each timetracking as session, index}
        <tr class="border-b hover:bg-gray-50">
            <td><span class="">{formatDate(session.session_date)}</span></td>
            <td><span class="">{session.service_code}</span></td>
            <td><span class="">{session.staff_name}</span></td>
            <td><span class="">{session.planmanager_name}</span></td>
            {#if session.session_duration == null || session.session_duration <= 0}
                <td colspan="2" class="text-center text-xs"> Note only</td>
            {:else}
                <td class="text-center text-xs">
                    {@html getOption(session.claim_type)}
                </td>
                <td class="text-center"
                    >{#if session.session_duration}{@html convertMinutesToHoursAndMinutes(
                            session.session_duration,
                        )}{:else}...{/if}
                    {#if session.actual_travel_time > 0}<span class="text-sm"
                            ><br />+ {@html convertMinutesToHoursAndMinutes(
                                session.actual_travel_time,
                            )} travel
                        </span>{/if}
                    {#if session.kilometers_travelled}<span class="text-sm"
                            >({session.kilometers_travelled}km)</span
                        >{/if}
                </td>
            {/if}

            <td>
                {#if session.claim_type == "TRAN"}
                    <button
                        on:click={() => push("/trips/" + session.trip_id)}
                        type="button"
                        class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                        >Edit</button
                    >
                {:else}
                    <button
                        on:click={() => push("/billables/" + session.id)}
                        type="button"
                        class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                        >Edit</button
                    >
                {/if}

                <!-- <a
                    class="inline-flex justify-center rounded-md bg-blue-600 px-1 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                    href="/#/billables/{session.id}">Edit</a
                > -->
            </td>
        </tr>
    {:else}
        <tr class="border-b">
            <td colspan="5" class="text-center p-4">No billable items found</td>
        </tr>
    {/each}
</table>
