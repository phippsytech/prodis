<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import {
        convertDateToUnixTimestamp,
        formatCurrency,
        getDaysUntilDate,
        formatDate,
        convertMinutesToHoursAndMinutes,
    } from "@shared/utilities.js";

    export let client_id = null;
    export let service_id = null;

    // future: last_seen_by_staff_id
    const CrystelCareStartTimestamp = convertDateToUnixTimestamp("2022-12-16");
    const day_in_seconds = 60 * 60 * 24;
    const week_in_seconds = 60 * 60 * 24 * 7;
    let budget_display = "total";

    let weeks = 0;
    let elapsed_weeks = 0;

    let currentTimestamp = Math.floor(Date.now() / 1000);
    let service_agreement_signed_date = CrystelCareStartTimestamp;

    let budget_total_minutes = 0;
    let budget_minutes_per_week = 0;
    let budget_elapsed_minutes = 0; // how many minutes should have been used to stay on budget
    let budget_elapsed_minutes_percentage = 0;
    let actual_elapsed_minutes = 0; // how many minutes should have been used to stay on budget
    let actual_elapsed_minutes_percentage = 0;
    let budget_diff_actual_minutes = 0;
    let last_seen = 0; // how many days ago the client was last seen for this service

    let service_code;
    let service_agreement_end_date;
    let plan_manager_name;

    let billing_code;
    let support_item_name;
    let registration_group_name;
    let support_category_name;

    let xero_account_code;

    function reset() {
        budget_display = "total";
        weeks = 0;
        elapsed_weeks = 0;
        currentTimestamp = Math.floor(Date.now() / 1000);
        service_agreement_signed_date = CrystelCareStartTimestamp;
        budget_total_minutes = 0;
        budget_minutes_per_week = 0;
        budget_elapsed_minutes = 0; // how many minutes should have been used to stay on budget
        budget_elapsed_minutes_percentage = 0;
        actual_elapsed_minutes = 0; // how many minutes should have been used to stay on budget
        actual_elapsed_minutes_percentage = 0;
        budget_diff_actual_minutes = 0;
        last_seen = 0; // how many days ago the client was last seen for this service
    }

    onMount(() => {
        let data = {};
        data.client_id = client_id;
        data.service_id = service_id;

        getClientServiceSummary(data);
    });

    $: {
        let data = {};
        data.client_id = client_id;
        data.service_id = service_id;
        getClientServiceSummary(data);
    }

    function getClientServiceSummary(data) {
        jspa("/Client/Plan/Service", "getClientServiceSummary", data).then(
            (result) => {
                reset();
                if (result.service.budget_display != null)
                    budget_display = result.service.budget_display;

                if (
                    CrystelCareStartTimestamp <
                    convertDateToUnixTimestamp(
                        result.service.service_agreement_signed_date,
                    )
                ) {
                    service_agreement_signed_date = convertDateToUnixTimestamp(
                        result.service.service_agreement_signed_date,
                    );
                }

                service_agreement_end_date = convertDateToUnixTimestamp(
                    result.service.service_agreement_end_date,
                );
                elapsed_weeks = Math.ceil(
                    (currentTimestamp - service_agreement_signed_date) /
                        week_in_seconds,
                );

                weeks = Math.round(
                    (service_agreement_end_date -
                        service_agreement_signed_date) /
                        week_in_seconds,
                );
                service_code = result.service.service_code;

                (billing_code = result.service.billing_code),
                    (support_item_name = result.service.support_item_name),
                    (registration_group_name =
                        result.service.registration_group_name),
                    (support_category_name =
                        result.service.support_category_name),
                    (xero_account_code = result.service.xero_account_code);
                plan_manager_name = result.service.plan_manager_name;
                budget_total_minutes = Math.round(
                    (result.service.budget / result.service.service_rate) * 60,
                );
                budget_minutes_per_week = Math.round(
                    budget_total_minutes / weeks,
                );
                budget_elapsed_minutes = parseInt(
                    elapsed_weeks * budget_minutes_per_week,
                ); // how many minutes should have been used to stay on budget
                budget_elapsed_minutes_percentage = Math.floor(
                    (budget_elapsed_minutes / budget_total_minutes) * 100,
                );

                actual_elapsed_minutes = parseInt(
                    result.timetracking.total_session_minutes,
                ); // how many minutes should have been used to stay on budget

                actual_elapsed_minutes_percentage = Math.floor(
                    (actual_elapsed_minutes / budget_total_minutes) * 100,
                );
                budget_diff_actual_minutes =
                    budget_elapsed_minutes - actual_elapsed_minutes;

                if (result.last_seen != null) {
                    last_seen = convertDateToUnixTimestamp(
                        result.last_seen.session_date,
                    ); // how many days ago the client was last seen for this service
                    last_seen = Math.ceil(
                        (currentTimestamp - last_seen) / day_in_seconds,
                    );
                } else {
                    last_seen = null;
                }
            },
        );
    }
</script>

<div class="cursor-pointer"></div>

<!-- DON'T FORGET TRAVEL TIME -->

{#if service_agreement_end_date < currentTimestamp}
    <!-- {#if budget_minutes_per_week < 0} -->
    <div class="font-bold text-red-600">CHECK NDIS END DATE</div>{/if}

{#if service_agreement_signed_date > currentTimestamp}
    <!-- {#if budget_minutes_per_week < 0} -->
    <div class="font-bold text-red-600">CHECK SERVICE AGREEMENT DATE</div>{/if}

{#if budget_display == "weekly"}
    <div
        class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
        style="line-height:0.8rem"
    >
        <div>
            <span class="">{service_code} </span>
            {#if budget_total_minutes}
                <span class="text-sm"
                    >{@html convertMinutesToHoursAndMinutes(
                        budget_total_minutes,
                    )} - {@html convertMinutesToHoursAndMinutes(
                        budget_minutes_per_week,
                    )}/wk</span
                >
            {:else}
                <span class="text-sm font-bold text-red-600">No Budget Set</span
                >
            {/if}
        </div>

        <span class="text-xs opacity-50 uppercase hidden sm:inline-block"
            >{plan_manager_name}
        </span>
    </div>

    {#if last_seen == null}
        <div class="text-xs text-red-600 mt-0 p-0 mb-1">
            No time has been recorded yet.
        </div>
    {:else}
        <div class="flex w-full h-2 bg-indigo-100">
            <div
                class="bg-indigo-950 h-full"
                style="width: {budget_elapsed_minutes_percentage}%"
            ></div>
            <!-- <div class="absolute text-white text-xs  px-1">{budget_elapsed_minutes}</div> -->
        </div>

        {#if budget_total_minutes}
            <div class="flex w-full h-6 bg-indigo-50">
                <div
                    class="bg-indigo-600 h-full"
                    style="width: {actual_elapsed_minutes_percentage}%"
                ></div>
                <div
                    class="absolute font-bold text-white text-sm px-1 drop-shadow-[0px_0px_8px_rgba(79,70,229,1)]"
                >
                    {@html convertMinutesToHoursAndMinutes(
                        actual_elapsed_minutes,
                    )} spent
                </div>
            </div>

            <div class="text-sm">
                {@html convertMinutesToHoursAndMinutes(
                    Math.abs(budget_diff_actual_minutes),
                )}
                {#if budget_diff_actual_minutes < 0}
                    over budget
                {/if}
                {#if budget_diff_actual_minutes == 0}
                    on budget
                {/if}
                {#if budget_diff_actual_minutes > 0}
                    of overflow
                {/if}
            </div>
        {/if}
    {/if}
{:else}
    <div
        class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
        style="line-height:0.8rem"
    >
        <div>
            <span class="">{service_code} </span>
            {#if budget_total_minutes}
                <span class="text-sm"
                    >{@html convertMinutesToHoursAndMinutes(
                        budget_total_minutes,
                    )} total</span
                >
            {:else}
                <span class="text-sm font-bold text-red-600">No Budget Set</span
                >
            {/if}
        </div>
        <span class="text-xs opacity-50 uppercase hidden sm:inline-block"
            >{plan_manager_name}
        </span>
    </div>

    {#if last_seen == null}
        <div class="text-xs text-red-600 mt-0 p-0 mb-1">
            No time has been recorded yet.
        </div>
    {:else if budget_total_minutes}
        <div class="flex w-full h-6 bg-indigo-50">
            <div
                class="bg-indigo-600 h-full"
                style="width: {actual_elapsed_minutes_percentage}%"
            ></div>

            <div
                class="absolute font-bold text-white text-sm px-1 drop-shadow-[0px_0px_8px_rgba(79,70,229,1)]"
            >
                {@html convertMinutesToHoursAndMinutes(actual_elapsed_minutes)} spent
            </div>
        </div>
        {#if budget_total_minutes - actual_elapsed_minutes > 0}
            {@html convertMinutesToHoursAndMinutes(
                budget_total_minutes - actual_elapsed_minutes,
            )} left
        {:else}
            No more budget available
        {/if}
    {/if}
{/if}
