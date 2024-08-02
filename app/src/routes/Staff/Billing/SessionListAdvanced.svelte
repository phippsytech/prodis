<script>
    import Container from "@shared/Container.svelte";
    import { PencilIcon } from "heroicons-svelte/24/outline";
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
    import { onMount, onDestroy } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { getClient } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";

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
    let filter = {
        start_date: "2023-06-19",
        end_date: "2023-06-25",
    };
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
                { url: null, name: "Time Tracking" },
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

<Container>
    <div class="text-xl font-medium">
        Filter <svg
            class="h-5 w-5 inline"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"
            ></path>
        </svg>
    </div>
    <div class="text-sm mb-1 font-bold opacity-50">Service</div>
    <div class="mb-2">
        {#each service_codes as service_code}
            <span
                on:click={() => toggleServiceCode(service_code)}
                class="inline-flex items-center gap-x-1.5 rounded-md px-1.5 py-0.5 text-sm font-medium cursor-pointer {selected_service_codes.includes(
                    service_code,
                )
                    ? 'bg-indigo-600 text-white'
                    : 'bg-gray-100 text-gray-600'} m-2"
            >
                {service_code}
            </span>
        {/each}
    </div>
    <div class="text-sm mb-1 font-bold opacity-50">Period</div>
    <div class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2">
        <div class="flex-grow">
            <FloatingDateSelect
                on:change={() => filterTimeTrackings()}
                label="Start Date"
                bind:value={filter.start_date}
                {start_year}
                {end_year}
            />
        </div>
        <div class="flex-grow">
            <FloatingDateSelect
                on:change={() => filterTimeTrackings()}
                label="End Date"
                bind:value={filter.end_date}
                {start_year}
                {end_year}
            />
        </div>
    </div>
</Container>

<table class="min-w-full">
    <thead>
        <tr class="border-b">
            <th class="text-left text-xs px-2"
                ><input
                    id="all"
                    name="comments"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                /> All</th
            >
            <th class="text-left text-xs px-2">Date</th>
            <th class="text-left text-xs px-2">Service</th>
            <th class="text-xs px-2">Claim Type</th>
            <th class="text-xs px-2">Duration</th>
            <th></th>
        </tr>
    </thead>
    {#each timetrackingList as session, index}
        <tr class="border-b">
            <td
                ><span class="text-left text-xs px-2">
                    <input
                        id="session{index}"
                        name="comments"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                    /></span
                ></td
            >
            <td><span class="">{session.session_date}</span></td>
            <td><span class="">{session.service_code}</span></td>
            {#if session.session_duration != null || session.session_duration > 0}
                <td class="text-center text-xs">
                    {@html getOption(session.claim_type)}
                </td>
                <td class="text-center"
                    >{#if session.session_duration}{session.session_duration}min{:else}...{/if}
                    {#if session.actual_travel_time > 0}<span class="text-sm"
                            ><br />+ {session.actual_travel_time}min travel
                        </span>{/if}
                    {#if session.kilometers_travelled}<span class="text-sm"
                            >({session.kilometers_travelled}km)</span
                        >{/if}
                </td>
            {/if}
            <td>
                <a
                    href="/#/clients/{session.client_id}/timetracking/{session.id}"
                    ><PencilIcon class="w-4 h-4" /></a
                >
            </td>
        </tr>
    {:else}
        <tr class="border-b">
            <td colspan="5" class="text-center p-4">No sessions found</td>
        </tr>
    {/each}
</table>
