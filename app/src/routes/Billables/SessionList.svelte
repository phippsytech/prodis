<script>
    import { PencilIcon } from "heroicons-svelte/24/outline";
    import { jspa } from "@shared/jspa.js";
    import { StafferStore, BreadcrumbStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";

    $: stafferStore = $StafferStore;

    export let billed = false;

    let timetracking = [];

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

    $: {
        jspa("/TimeTracking", "getTimeTrackingSummary", {
            staff_id: stafferStore.id,
            billed: billed,
        })
            .then((result) => {
                timetracking = result.result;
            })
            .catch((error) => (timetracking = []));
    }

    BreadcrumbStore.set({ path: [{ url: "/billables", name: "Billables" }] });
</script>

<table class="min-w-full">
    <thead>
        <tr class="border-b">
            <th class="text-left text-xs px-2">Client</th>
            <th class="text-xs px-2">Claim Type</th>
            <th class="text-xs px-2">Duration</th>

            <th></th>
        </tr>
    </thead>
    {#each timetracking as session, index}
        <tr class="border-b">
            <td
                ><span class="text-xs"
                    >{session.session_date} {session.service_code}</span
                ><br /><a
                    class="font-bold text-blue-600 underline"
                    href="/#/clients/{session.client_id}"
                    >{session.client_name}</a
                >
            </td>
            {#if session.session_duration == null || session.session_duration <= 0}
                <td colspan="2" class="text-center text-xs"> Note only</td>
            {:else}
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

                <!-- <a href="/#/billables/{session.id}"
                    ><PencilIcon class="w-4 h-4" /></a
                > -->
            </td>
        </tr>
    {:else}
        <tr class="border-b">
            <td colspan="5" class="text-center p-4">No sessions found</td>
        </tr>
    {/each}
</table>
