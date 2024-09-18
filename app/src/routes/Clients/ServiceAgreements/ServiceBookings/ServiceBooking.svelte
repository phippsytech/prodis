<script>
    import { convertMinutesToHoursAndMinutes } from "@shared/utilities.js";
    import BudgetBar from "@shared/BudgetBar.svelte";
    import BudgetBarWeekly from "@shared/BudgetBarWeekly.svelte";
    import { formatDate, timeAgo } from "@shared/utilities.js";
    import { jspa } from "@shared/jspa.js";

    // Export service agreement and service objects

    // // export let service_booking.id = null;
    export let service_booking = {};

    // Listen for the "refresh" event from the parent
    // $: if (service_booking.id) {
    //     loadServiceBooking();
    // }

    // async function loadServiceBooking() {
    //     if (service_booking.id) {
    //         try {
    //             const result = await jspa(
    //                 "/Participant/ServiceBooking",
    //                 "getParticipantServiceBooking",
    //                 {
    //                     id: service_booking.id,
    //                 },
    //             );
    //             service_booking = result.result;
    //         } catch (error) {
    //             console.error("Error fetching service booking:", error);
    //         }
    //     }
    // }

    // Function to calculate hours per week based on allocated hours and date range
    function hoursPerWeek(allocatedHours, startDate, endDate) {
        // Initialize start and end dates
        startDate = startDate
            ? startDate
            : new Date(service_booking.budget_start_date);

        endDate = endDate
            ? endDate
            : new Date(service_booking.service_agreement_end_date);

        // Get the difference in time (in milliseconds)
        const timeInterval = endDate.getTime() - startDate.getTime();

        // Convert time difference from milliseconds to days
        const totalDaysInTheServiceDateRange =
            timeInterval / (1000 * 3600 * 24);

        const totalWeeks = (totalDaysInTheServiceDateRange / 7).toFixed(2);

        // Calculate daily hours
        const dailyHours = allocatedHours / totalDaysInTheServiceDateRange;

        // Calculate total hours per week
        let result = dailyHours * 7;

        return result;
    }

    function getRemainingWeeks(startDate, endDate) {
        // Parse the dates
        const start = new Date(startDate).getTime();
        const end = new Date(endDate).getTime();
        const current = new Date().getTime();

        //Ensure the current date is within the interval
        if (current < start || current > end) {
            console.log("Current date is outside the service interval.");
            return 0;
        }

        // Calculate the remaining duration in milliseconds
        const remainingDurationInMs = end - current;
        const remainingDurationInDays =
            remainingDurationInMs / (1000 * 60 * 60 * 24);
        // Calculate weeks
        const remainingWeeks = remainingDurationInDays / 7;

        return remainingWeeks;
    }

    function totalRemainingBudgetInMinutes() {
        const minutesSpent =
            (service_booking.spent / service_booking.rate) * 60;

        const totalBudgetInHours =
            service_booking.budget / service_booking.rate;

        let totalBudgetInMinutes = totalBudgetInHours * 60;

        totalBudgetInMinutes -= minutesSpent;

        if (service_booking.spend_status && service_booking.spend_status < 0) {
            console.log("over budget " + service_booking.spend_status);

            totalBudgetInMinutes -= Math.abs(service_booking.spend_status);
        }

        return totalBudgetInMinutes;
    }

    function getAdjustedWeeklyTimeInMinutes(startDate, endDate) {
        const remainingMinutes = totalRemainingBudgetInMinutes();

        let remainingWeeks = getRemainingWeeks(startDate, endDate);

        if (remainingWeeks < 1 && remainingWeeks != 0) {
            return remainingMinutes;
        }

        const adjustedWeeklyTime = remainingMinutes / remainingWeeks;

        return adjustedWeeklyTime;
    }
</script>

<div class={service_booking.is_active ? "" : "opacity-50"}>
    {#if service_booking.budget_display == "weekly"}
        <div class="block w-full">
            <div
                class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
                style="line-height:0.8rem"
            >
                <div>
                    <span class="text-slate-800 font-bold"
                        >{service_booking.code}</span
                    >
                    {#if service_booking.budget && service_booking.budget > 0}
                        <span class="text-slate-600">
                            {@html convertMinutesToHoursAndMinutes(
                                (service_booking.budget /
                                    service_booking.rate) *
                                    60,
                            )}
                        </span>

                        {#if service_booking.adjust_weekly_time}
                            {#if hoursPerWeek(service_booking.remainingMinutes / 60, new Date(), new Date(service_booking.service_agreement_end_date)) != 0}
                                <span class="text-xs text-slate-400 ml-1">
                                    ({@html convertMinutesToHoursAndMinutes(
                                        getAdjustedWeeklyTimeInMinutes(
                                            new Date(
                                                service_booking.budget_start_date,
                                            ),
                                            new Date(
                                                service_booking.service_agreement_end_date,
                                            ),
                                        ),
                                    )} / wk )
                                </span>
                            {/if}
                        {:else if hoursPerWeek(service_booking.totalServiceDuration / 60, new Date(service_booking.service_agreement_signed_date), new Date(service_booking.service_agreement_end_date)) > 1}
                            <span class="italic text-xs text-slate-400">
                                ({@html convertMinutesToHoursAndMinutes(
                                    hoursPerWeek(
                                        service_booking.budget /
                                            service_booking.rate,
                                        new Date(
                                            service_booking.service_agreement_signed_date,
                                        ),
                                        new Date(
                                            service_booking.service_agreement_end_date,
                                        ),
                                    ) * 60,
                                )} / wk)
                            </span>
                        {/if}
                    {/if}
                    {#if !service_booking.is_active}
                        - NOT ACTIVE
                    {/if}
                </div>
                <span
                    class="text-xs text-indigo-300 uppercase hidden sm:inline-block"
                >
                    {service_booking.plan_manager_name}
                </span>
            </div>
            <BudgetBarWeekly
                totalBudget={service_booking.budget}
                hourlyRate={service_booking.rate}
                spentBudget={service_booking.spent}
                startDate={service_booking.budget_start_date}
                endDate={service_booking.service_agreement_end_date}
                bind:spendStatus={service_booking.spend_status}
                bind:remainingBudget={service_booking.remainingBudget}
                bind:remainingMinutes={service_booking.remainingMinutes}
                bind:totalServiceDuration={service_booking.totalServiceDuration}
            />

            {#if service_booking.budget && service_booking.budget > 0}
                {#if !service_booking.adjust_weekly_time && service_booking.spend_status != 0}
                    <span class="text-xs text-slate-400 ml-1">
                        {@html convertMinutesToHoursAndMinutes(
                            Math.abs(service_booking.spend_status),
                        )}
                        {service_booking.spend_status > 0
                            ? "of overflow"
                            : "over budget"}
                    </span>
                {/if}
            {/if}
        </div>
    {:else}
        <div class="block w-full">
            <div
                class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
                style="line-height:0.8rem"
            >
                <div>
                    <span class="text-slate-800 font-bold"
                        >{service_booking.code}</span
                    >
                    <span class="text-slate-600">
                        {@html convertMinutesToHoursAndMinutes(
                            (service_booking.budget / service_booking.rate) *
                                60,
                        )}
                    </span>
                    {#if !service_booking.is_active}
                        - NOT ACTIVE
                    {/if}
                </div>
                <span
                    class="text-xs text-indigo-300 uppercase hidden sm:inline-block"
                >
                    {service_booking.plan_manager_name}
                </span>
            </div>
            <BudgetBar
                totalBudget={service_booking.budget}
                hourlyRate={service_booking.rate}
                spentBudget={service_booking.spent}
                bind:remainingMinutes={service_booking.remainingMinutes}
            />

            {#if service_booking.last_session_date && service_booking.budget && service_booking.budget > 0 && service_booking.rate && service_booking.remainingMinutes > 1}
                <span class="text-xs text-slate-400 ml-1">
                    {@html convertMinutesToHoursAndMinutes(
                        service_booking.remainingMinutes,
                    )} remaining
                </span>
            {/if}
        </div>
    {/if}

    {#if service_booking.last_session_date}
        <div class="px-1 text-xs text-slate-400">
            Last billed {timeAgo(service_booking.last_session_date)}
            {formatDate(service_booking.last_session_date)}
        </div>
    {/if}
</div>
