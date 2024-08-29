<script>
    // Import necessary utilities and components
    import { convertMinutesToHoursAndMinutes } from "@shared/utilities.js";
    import BudgetBar from "@shared/BudgetBar.svelte";
    import BudgetBarWeekly from "@shared/BudgetBarWeekly.svelte";
    import { formatDate, timeAgo } from "@shared/utilities.js";

    // Export service agreement and service objects
    export let service_agreement = {};
    export let service = {};

    // Function to calculate the number of weeks between two dates
    function weeksLeft(startDate, endDate) {
        // Get the difference in time (in milliseconds)
        const timeDifference = endDate.getTime() - startDate.getTime();

        // Convert time difference from milliseconds to days
        const daysDifference = timeDifference / (1000 * 3600 * 24);

        // Convert days to weeks
        const weeksDifference = daysDifference / 7;

        return weeksDifference;
    }

    // Function to calculate hours per week based on allocated hours and date range
    function hoursPerWeek(allocatedHours, startDate, endDate) {
        console.log(service);

        // Initialize start and end dates
        startDate = startDate ? startDate : new Date(service.budget_start_date);

        endDate = endDate
            ? endDate
            : new Date(service_agreement.service_agreement_end_date);

        console.log("start date: " + startDate);

        console.log("end date: " + endDate);

        // Get the difference in time (in milliseconds)
        const timeInterval = endDate.getTime() - startDate.getTime();

        // Convert time difference from milliseconds to days
        const totalDaysInTheServiceDateRange =
            timeInterval / (1000 * 3600 * 24);

        const totalWeeks = (totalDaysInTheServiceDateRange / 7).toFixed(2);

        console.log("total weeks: " + totalWeeks);

        console.log("days difference " + totalDaysInTheServiceDateRange);

        console.log("allocated hours " + allocatedHours);

        // Calculate daily hours
        const dailyHours = allocatedHours / totalDaysInTheServiceDateRange;
        console.log("daily hours " + dailyHours);

        // Calculate total hours per week
        let result = dailyHours * 7;

        console.log("weekly hour " + result);
        console.log("weekly hour in minutes " + result * 60);
        return result;
    }

    function getRemainingWeeks(startDate, endDate) {
        // Parse the dates
        const start = new Date(startDate).getTime();
        const end = new Date(endDate).getTime();
        const current = new Date().getTime();

        console.log("r start date: " + start);

        //Ensure the current date is within the interval
        if (current < start || current > end) {
            console.log("Current date is outside the service interval.");
            return 0;
        }

        // Calculate the remaining duration in milliseconds
        const remainingDurationInMs = end - current;
        console.log("remainingDurationInMs " + remainingDurationInMs);
        const remainingDurationInDays =
            remainingDurationInMs / (1000 * 60 * 60 * 24);
        console.log("remainingDurationInDays " + remainingDurationInDays);
        // Calculate weeks
        const remainingWeeks = remainingDurationInDays / 7;

        console.log("remaining weeks: " + remainingWeeks);

        return remainingWeeks;
    }

    function totalRemainingBudgetInMinutes() {
        const minutesSpent = (service.spent / service.rate) * 60;

        console.log("minutes Spent: " + minutesSpent);

        const totalBudgetInHours = service.budget / service.rate;

        console.log("total budget in hours: " + totalBudgetInHours);

        let totalBudgetInMinutes = totalBudgetInHours * 60;

        totalBudgetInMinutes -= minutesSpent;

        if (service.spend_status && service.spend_status < 0) {
            console.log("over budget " + service.spend_status);

            totalBudgetInMinutes -= Math.abs(service.spend_status);
        }

        console.log("total budget in minutes: " + totalBudgetInMinutes);

        return totalBudgetInMinutes;
    }

    function getAdjustedWeeklyTimeInMinutes(startDate, endDate) {
        const remainingMinutes = totalRemainingBudgetInMinutes();

        let remainingWeeks = getRemainingWeeks(startDate, endDate);

        if (remainingWeeks < 1 && remainingWeeks != 0) {
            return remainingMinutes;
        }

        const adjustedWeeklyTime = remainingMinutes / remainingWeeks;

        console.log("adjusted weekly time: " + adjustedWeeklyTime);

        return adjustedWeeklyTime;
    }
</script>

<div class={service.is_active ? "" : "opacity-50"}>
    {#if service.budget_display == "weekly"}
        <div class="block w-full">
            <div
                class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
                style="line-height:0.8rem"
            >
                <div>
                    <span class="text-slate-800 font-bold">{service.code}</span>
                    {#if service.budget && service.budget > 0}
                        <span class="text-slate-600">
                            {@html convertMinutesToHoursAndMinutes(
                                (service.budget / service.rate) * 60,
                            )}
                        </span>

                        {#if service.adjust_weekly_time}
                            {#if hoursPerWeek(service.remainingMinutes / 60, new Date(), new Date(service_agreement.service_agreement_end_date)) != 0}
                                <span class="text-xs text-slate-400 ml-1">
                                    ({@html convertMinutesToHoursAndMinutes(
                                        getAdjustedWeeklyTimeInMinutes(
                                            new Date(service.budget_start_date),
                                            new Date(
                                                service_agreement.service_agreement_end_date,
                                            ),
                                        ),
                                    )} / wk )
                                </span>
                            {/if}
                        {:else if hoursPerWeek(service.totalServiceDuration / 60, new Date(service_agreement.service_agreement_signed_date), new Date(service_agreement.service_agreement_end_date)) > 1}
                            <span class="italic text-xs text-slate-400">
                                ({@html convertMinutesToHoursAndMinutes(
                                    hoursPerWeek(
                                        service.budget / service.rate,
                                        new Date(
                                            service_agreement.service_agreement_signed_date,
                                        ),
                                        new Date(
                                            service_agreement.service_agreement_end_date,
                                        ),
                                    ) * 60,
                                )} / wk)
                            </span>
                        {/if}
                    {/if}
                    {#if !service.is_active}
                        - NOT ACTIVE
                    {/if}
                </div>
                <span
                    class="text-xs text-indigo-300 uppercase hidden sm:inline-block"
                >
                    {service.plan_manager_name}
                </span>
            </div>
            <BudgetBarWeekly
                totalBudget={service.budget}
                hourlyRate={service.rate}
                spentBudget={service.spent}
                startDate={service.budget_start_date}
                endDate={service_agreement.service_agreement_end_date}
                bind:spendStatus={service.spend_status}
                bind:remainingBudget={service.remainingBudget}
                bind:remainingMinutes={service.remainingMinutes}
                bind:totalServiceDuration={service.totalServiceDuration}
            />
            {#if service.budget && service.budget > 0}
                {#if !service.adjust_weekly_time && service.spend_status != 0}
                    <span class="text-xs text-slate-400 ml-1">
                        {@html convertMinutesToHoursAndMinutes(
                            Math.abs(service.spend_status),
                        )}
                        {service.spend_status > 0
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
                    <span class="text-slate-800 font-bold">{service.code}</span>
                    <span class="text-slate-600">
                        {@html convertMinutesToHoursAndMinutes(
                            (service.budget / service.rate) * 60,
                        )}
                    </span>
                    {#if !service.is_active}
                        - NOT ACTIVE
                    {/if}
                </div>
                <span
                    class="text-xs text-indigo-300 uppercase hidden sm:inline-block"
                >
                    {service.plan_manager_name}
                </span>
            </div>
            <BudgetBar
                totalBudget={service.budget}
                hourlyRate={service.rate}
                spentBudget={service.spent}
                bind:remainingMinutes={service.remainingMinutes}
            />
            {#if service.last_session_date && service.budget && service.budget > 0 && service.rate && service.remainingMinutes > 1}
                <span class="text-xs text-slate-400 ml-1">
                    {@html convertMinutesToHoursAndMinutes(
                        service.remainingMinutes,
                    )} remaining
                </span>
            {/if}
        </div>
    {/if}

    {#if service.last_session_date}
        <div class="px-1 text-xs text-slate-400">
            Last billed {timeAgo(service.last_session_date)}
            {formatDate(service.last_session_date)}
        </div>
    {/if}
</div>
