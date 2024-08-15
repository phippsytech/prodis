<script>
    import { getLocalDate } from "@shared/utilities.js";
    import BudgetBar from "@shared/BudgetBar.svelte";

    export let totalBudget = 0;
    export let hourlyRate = 0;
    export let spentBudget = 0;

    // TODO: what do I replace these hardcoded dates with?
    export let startDate = null;
    export let endDate = null;

    // exporting the following so they can be used in the parent component
    export let spendStatus = 0;
    export let remainingBudget = 0;
    export let remainingMinutes = 0;
    export let totalServiceDuration = 0;

    let totalAvailableMinutes;
    let spentMinutes;
    let elapsedTime;
    let expectedSpendToDate;
    let expectedSpendPercentageToDate;

    $: {
        let startDateObj = new Date(startDate).getTime();
        let endDateObj = new Date(endDate).getTime();
        let currentDateObj = new Date(getLocalDate()).getTime();

        elapsedTime = (currentDateObj - startDateObj) / 60000; // in minutes
        totalServiceDuration = (endDateObj - startDateObj) / 60000; // in minutes

        // the tenary operators are being used to prevent division by zero
        totalAvailableMinutes = hourlyRate
            ? (totalBudget / hourlyRate) * 60
            : 0; // in minutes

        spentMinutes = hourlyRate ? (spentBudget / hourlyRate) * 60 : 0; // in minutes

        expectedSpendPercentageToDate = totalServiceDuration
            ? (elapsedTime / totalServiceDuration) * 100
            : 0;

        expectedSpendToDate = totalAvailableMinutes
            ? (expectedSpendPercentageToDate / 100) * totalAvailableMinutes
            : 0;

        // spendStatus = spentMinutes - expectedSpendToDate;
        spendStatus = expectedSpendToDate - spentMinutes;
        remainingBudget = totalBudget - spentBudget;
        remainingMinutes = totalAvailableMinutes - spentMinutes;
    }
</script>

{#if totalBudget != 0 && spentBudget != 0 && hourlyRate != 0}
    <div class="flex w-full h-2 bg-indigo-100">
        <div
            class="bg-indigo-950 h-full"
            style="width: {expectedSpendPercentageToDate}%"
        ></div>
    </div>
{/if}

<BudgetBar bind:totalBudget bind:hourlyRate bind:spentBudget weekly={true} />
