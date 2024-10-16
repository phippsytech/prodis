<script>
  import {
    convertMinutesToHoursAndMinutes,
    formatDate,
    timeAgo,
  } from "@shared/utilities.js";
  import BudgetBar from "@shared/BudgetBar.svelte";
  import BudgetBarWeekly from "@shared/BudgetBarWeekly.svelte";
  import ExpiredServiceAgreementBudgetBar from "@shared/ExpiredServiceAgreementBudgetBar.svelte";

  export let service_booking = {};

  let spendStatus = 0;
  let serviceBookingRemaingingMinutes = 0;
  let totalServiceBookingDuration = 0;

  // Create a derived object with necessary adjustments
  $: derivedServiceBooking = {
    ...service_booking,
    budget: calculateAdjustedBudget(service_booking),
  };

  // Function to adjust the budget by deducting the remaining kilometers
  function calculateAdjustedBudget(service_booking) {
    if (
      !service_booking.include_travel ||
      service_booking.kilometer_budget == null ||
      service_booking.kilometer_budget == 0
    )
      return service_booking.budget;

    // const remainingKmBudget = Math.max(
    //   service_booking.kilometer_budget - service_booking.spent_km,
    //   0
    // );
    // const kmRatePerUnit = 1; // service_booking.rate / 60; // Assuming the rate is per hour, adjust accordingly
    // const kmDeduction = remainingKmBudget * kmRatePerUnit;

    // Return the adjusted budget
    return service_booking.budget - service_booking.kilometer_budget;
  }

  // Helper functions for calculating weekly hours and remaining weeks
  function hoursPerWeek(allocatedHours, startDate, endDate) {
    const timeInterval = (endDate - startDate) / (1000 * 3600 * 24); // Convert ms to days
    return (allocatedHours / timeInterval) * 7;
  }

  function getRemainingWeeks(startDate, endDate) {
    const current = new Date().setHours(0, 0, 0, 0);
    if (current <= startDate || current >= endDate) {
      return 0;
    }
    return (endDate - current) / (1000 * 60 * 60 * 24 * 7);
  }

  // Calculate total remaining budget in minutes
  function totalRemainingBudgetInMinutes() {
    const totalBudgetInMinutes =
      (derivedServiceBooking.budget / derivedServiceBooking.rate) * 60;
    const minutesSpent =
      (derivedServiceBooking.spent / derivedServiceBooking.rate) * 60;

    let remainingBudgetInMinutes = totalBudgetInMinutes - minutesSpent;

    if (spendStatus < 0) {
      remainingBudgetInMinutes -= Math.abs(spendStatus);
    }

    return remainingBudgetInMinutes;
  }

  // Get adjusted weekly time in minutes
  function getAdjustedWeeklyTimeInMinutes(startDate, endDate) {
    const remainingMinutes = totalRemainingBudgetInMinutes();
    const remainingWeeks = getRemainingWeeks(startDate, endDate);
    return remainingWeeks < 1 && remainingWeeks !== 0
      ? remainingMinutes
      : remainingMinutes / remainingWeeks;
  }

  $: isExpired = (() => {
    const current = new Date().setHours(0, 0, 0, 0);
    const start = new Date(
      derivedServiceBooking.service_agreement_signed_date
    ).getTime();
    const end = new Date(
      derivedServiceBooking.service_agreement_end_date
    ).getTime();
    return current <= start || current >= end;
  })();
</script>

{#if isExpired}
  <div class="block w-full">
    <div
      class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
      style="line-height:0.8rem"
    >
      <div>
        <span class="text-slate-800 font-bold"
          >{derivedServiceBooking.code}</span
        >
        {#if !derivedServiceBooking.is_active}
          - NOT ACTIVE
        {/if}
      </div>
      <span class="text-xs text-indigo-300 uppercase hidden sm:inline-block"
        >{derivedServiceBooking.plan_manager_name}</span
      >
    </div>
    <ExpiredServiceAgreementBudgetBar />
  </div>
{:else}
  <div class={derivedServiceBooking.is_active ? "" : "opacity-50"}>
    <div class="block w-full">
      <div
        class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
        style="line-height:0.8rem"
      >
        <div>
          <span class="text-slate-800 font-bold"
            >{derivedServiceBooking.code}</span
          >
          {#if derivedServiceBooking.budget > 0}
            <span class="text-slate-600">
              {@html convertMinutesToHoursAndMinutes(
                (derivedServiceBooking.budget / derivedServiceBooking.rate) * 60
              )}
            </span>
            {#if derivedServiceBooking.adjust_weekly_time}
              {#if hoursPerWeek(serviceBookingRemaingingMinutes / 60, new Date(), new Date(derivedServiceBooking.service_agreement_end_date)) != 0}
                <span class="text-xs text-slate-400 ml-1">
                  ({@html convertMinutesToHoursAndMinutes(
                    getAdjustedWeeklyTimeInMinutes(
                      new Date(derivedServiceBooking.budget_start_date),
                      new Date(derivedServiceBooking.service_agreement_end_date)
                    )
                  )} / wk)
                </span>
              {/if}
            {:else if hoursPerWeek(totalServiceBookingDuration / 60, new Date(derivedServiceBooking.service_agreement_signed_date), new Date(derivedServiceBooking.service_agreement_end_date)) > 1}
              <span class="italic text-xs text-slate-400">
                ({@html convertMinutesToHoursAndMinutes(
                  hoursPerWeek(
                    derivedServiceBooking.budget / derivedServiceBooking.rate,
                    new Date(
                      derivedServiceBooking.service_agreement_signed_date
                    ),
                    new Date(derivedServiceBooking.service_agreement_end_date)
                  ) * 60
                )} / wk)
              </span>
            {/if}
          {/if}
          {#if !derivedServiceBooking.is_active}
            - NOT ACTIVE
          {/if}
        </div>
        <span class="text-xs text-indigo-300 uppercase hidden sm:inline-block"
          >{derivedServiceBooking.plan_manager_name}</span
        >
      </div>

      {#if derivedServiceBooking.budget_display == "weekly"}
        <BudgetBarWeekly
          totalBudget={derivedServiceBooking.budget}
          hourlyRate={derivedServiceBooking.rate}
          spentBudget={derivedServiceBooking.spent}
          startDate={derivedServiceBooking.budget_start_date}
          endDate={derivedServiceBooking.service_agreement_end_date}
          bind:spendStatus
          bind:remainingBudget={derivedServiceBooking.remainingBudget}
          bind:remainingMinutes={serviceBookingRemaingingMinutes}
          bind:totalServiceDuration={totalServiceBookingDuration}
        />
        {#if derivedServiceBooking.budget > 0 && !derivedServiceBooking.adjust_weekly_time && spendStatus != 0}
          <span class="text-xs text-slate-400 ml-1">
            {@html convertMinutesToHoursAndMinutes(Math.abs(spendStatus))}
            {spendStatus > 0 ? "of overflow" : "over budget"}
          </span>
        {/if}
      {:else}
        <BudgetBar
          totalBudget={derivedServiceBooking.budget}
          hourlyRate={derivedServiceBooking.rate}
          spentBudget={derivedServiceBooking.spent}
          bind:remainingMinutes={serviceBookingRemaingingMinutes}
        />
        {#if derivedServiceBooking.last_session_date && serviceBookingRemaingingMinutes > 1}
          <span class="text-xs text-slate-400 ml-1">
            {@html convertMinutesToHoursAndMinutes(
              serviceBookingRemaingingMinutes
            )} remaining
          </span>
        {/if}
      {/if}

      {#if derivedServiceBooking.last_session_date}
        <div class="px-1 text-xs text-slate-400">
          Last billed {timeAgo(derivedServiceBooking.last_session_date)}
          {formatDate(derivedServiceBooking.last_session_date)}
        </div>
      {/if}
    </div>
  </div>
{/if}
