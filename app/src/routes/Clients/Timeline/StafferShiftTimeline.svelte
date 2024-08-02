<script>
    import TimelineActivity from "./TimelineActivity.svelte";
    import { push } from "svelte-spa-router";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { haveCommonElements, formatDate } from "@shared/utilities.js";
    import { getClient } from "@shared/api.js";

    import { RolesStore } from "@shared/stores.js";

    export let mode = "summary";
    export let selected_form_id = null;
    export let date;
    export let start_time;
    export let end_time;
    export let staff_id;
    export let client_id;

    // date="2023-06-17";
    // start_time="11:00";
    // end_time="12:00";

    // staff_id=57;

    let internal_reports = [
        "ParentalContact",
        "Parental Contact",
        "RiskAssessment",
        "Risk Assessment",
        "RiskAssessment",
        "Expense",
    ];

    $: rolesStore = $RolesStore;

    let client = {};
    let loading = true;
    let timeline = [];

    onMount(async () => {
        client = await getClient(client_id);
        getTimeline(client_id, staff_id, date, start_time, end_time);
    });

    function getTimeline(client_id, staff_id, date, start_time, end_time) {
        let summary = mode == "summary";
        jspa("/SIL/House/Form", "getStafferShiftTimeline", {
            participant_id: client_id,
            staff_id: staff_id,
            date: date,
            start_time: start_time,
            end_time: end_time,
        })
            .then((result) => {
                timeline = result.result;
            })
            .catch((error) => {})
            .finally(() => {
                loading = false;
            });
    }

    function handle(form_id) {
        push("/clients/" + client.id + "/timeline/" + form_id);
    }

    function checkVisibility(activity) {
        if (
            haveCommonElements(rolesStore, ["stakeholder"]) &&
            internal_reports.includes(activity.report_type)
        ) {
            return false;
        }

        return true;
    }

    $: {
        getTimeline(client_id, staff_id, date, start_time, end_time);
    }
</script>

<ul role="list" class="divide-y divide-gray-100">
    {#each timeline as activity, index}
        {#if checkVisibility(activity)}
            {#if index == 0 || timeline[index - 1].date != activity.date}
                <li
                    class="relative flex justify-between py-2 px-5 text-gray-700"
                >
                    <div class="min-w-0 flex-auto">
                        <p class="text-sm font-semibold leading-6">
                            {formatDate(activity.date)}
                        </p>
                    </div>
                </li>
            {/if}

            <!-- Current: "bg-gray-50 text-indigo-600", Default: "text-gray-700 hover:text-indigo-600 hover:bg-gray-50" -->
            <li
                class="relative flex justify-between py-2 px-5 {selected_form_id ==
                activity.id
                    ? 'bg-gray-50 text-indigo-600'
                    : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50'} "
            >
                <div
                    on:click={() => handle(activity.id)}
                    class="min-w-0 flex-auto"
                >
                    <TimelineActivity {activity} />
                </div>

                <div class="flex items-center justify-end gap-x-4 flex-none">
                    <svg
                        class="h-5 w-5 flex-none text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </li>
        {/if}
    {:else}
        {#if loading}
            <div
                class="flex justify-center items-center h-full text-center text-gray-400"
            >
                <svg
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    />
                </svg>
                Loading
            </div>
        {:else}
            <li class="relative flex justify-between py-2 px-5 text-gray-700">
                <div class="min-w-0 flex-auto text-center">
                    <p class="text-sm font-semibold leading-6">
                        No activity recorded
                    </p>
                </div>
            </li>
        {/if}
    {/each}

    <!-- {#if timeline.length > 0}
        {#if mode == "summary"}
            <li class="text-center">
                <a
                    href="/#/timeline"
                    class="inline-flex items-center px-3 py-1 my-1 space-x-2 text-sm border rounded-full group"
                    >View more&mldr;</a
                >
            </li>
        {/if}
    {/if} -->
</ul>
