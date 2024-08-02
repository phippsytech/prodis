<!-- <script>
    import { convertMinutesToHoursAndMinutes } from "@shared/utilities.js";

    export let totalBudget = 0;
    export let hourlyRate = 0;
    export let spentBudget = 0;
    export let remainingMinutes = 0;

    // the tenary operators are being used to prevent division by zero
    $: totalAvailableMinutes = hourlyRate ? (totalBudget / hourlyRate) * 60 : 0;
    $: spentMinutes = hourlyRate ? (spentBudget / hourlyRate) * 60 : 0;
    $: spentMinutesPercentage = totalAvailableMinutes
        ? (spentMinutes / totalAvailableMinutes) * 100
        : 0;

    $: remainingMinutes = totalAvailableMinutes - spentMinutes;
</script>

<div class="flex w-full h-6 bg-indigo-50">
    <div
        class="bg-indigo-600 h-full"
        style="width: {spentMinutesPercentage}%"
    ></div>

    <div
        class="absolute font-bold text-white text-sm px-1 drop-shadow-[0px_0px_8px_rgba(79,70,229,1)]"
    >
        {#if totalBudget == 0}
            No budget set.
        {:else if spentBudget == 0}
            No budget spent.
        {:else if hourlyRate == 0}
            No hourly rate set for service.
        {:else}
            {@html convertMinutesToHoursAndMinutes(spentMinutes)} spent
        {/if}
    </div>
</div> -->

<script>
    import { slide } from "svelte/transition";
    import { convertMinutesToHoursAndMinutes } from "@shared/utilities.js";

    export let weekly = false; // this is just a value that adjusts the y padding for weekly

    export let totalBudget = 0;
    export let hourlyRate = 0;
    export let spentBudget = 0;
    export let remainingMinutes = 0;

    // make sure the supplied values are treated as numbers
    $: totalBudget = Number(totalBudget);
    $: hourlyRate = Number(hourlyRate);
    $: spentBudget = Number(spentBudget);

    // the tenary operators are being used to prevent division by zero
    $: totalAvailableMinutes = hourlyRate ? (totalBudget / hourlyRate) * 60 : 0;
    $: spentMinutes = hourlyRate ? (spentBudget / hourlyRate) * 60 : 0;
    // $: spentMinutesPercentage = totalAvailableMinutes
    //     ? (spentMinutes / totalAvailableMinutes) * 100
    //     : 0;

    $: spentMinutesPercentage = totalAvailableMinutes
        ? Math.min((spentMinutes / totalAvailableMinutes) * 100, 100)
        : 0;

    $: overspentMinutesPercentage =
        spentBudget > totalBudget
            ? ((spentBudget - totalBudget) / totalBudget) * 100
            : 0;

    $: remainingMinutes = totalAvailableMinutes - spentMinutes;
</script>

<div class="flex w-full h-6 bg-indigo-50">
    <div
        in:slide|global={{ duration: 500, axis: "x" }}
        class="bg-indigo-600 h-full"
        style="width: {spentMinutesPercentage}%"
    ></div>
    {#if spentBudget > totalBudget}
        <div
            in:slide|global={{ duration: 1000, axis: "x" }}
            class="bg-red-600 h-full"
            style="width: {overspentMinutesPercentage}%"
        ></div>
    {/if}
    <div
        class="absolute font-bold text-white text-sm px-1 {weekly
            ? ''
            : 'py-0.5'} drop-shadow-[0px_0px_8px_rgba(79,70,229,1)]"
    >
        {#if totalBudget == 0}
            No budget set.
        {:else if spentBudget == 0}
            No budget spent.
        {:else if hourlyRate == 0}
            No hourly rate set for service.
        {:else}
            {@html convertMinutesToHoursAndMinutes(spentMinutes)}
        {/if}
    </div>
</div>
