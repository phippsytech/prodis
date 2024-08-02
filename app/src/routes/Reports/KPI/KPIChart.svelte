<script>
    import ExpandIndicator from "@app/Layout/ExpandIndicator.svelte";
    import { slide } from "svelte/transition";
    import {
        convertDateToUnixTimestamp,
        formatCurrency,
        getDaysUntilDate,
        formatDate,
        convertMinutesToHoursAndMinutes,
        convertDecimalHoursToHoursAndMinutes,
    } from "@shared/utilities.js";

    export let staffer = {}; // staff member

    // convert string to integer
    let kpi = parseFloat(staffer.billable_hours_kpi);
    let actual = 0;

    staffer.services.forEach((service) => {
        actual = actual + parseFloat(service.total_session_minutes) / 60;
    });

    let kpi_width = 0;
    let actual_width = 0;
    let error = false;

    if (kpi == 0 || actual == 0) {
        error = true;
    }

    if (error == false) {
        if (kpi > actual) {
            kpi_width = 100;
            actual_width = (actual / kpi) * 100;
        } else {
            kpi_width = (kpi / actual) * 100;
            actual_width = 100;
        }
    }

    // function @html convertDecimalHoursToHoursAndMinutes(decimalHours) {
    //     const minutes = decimalHours * 60;
    //     const hours = Math.floor(minutes / 60);
    //     const remainingMinutes = Math.floor(minutes % 60);
    //     let result = '';
    //     if (hours > 0) result = `${hours}hrs`;
    //     result += remainingMinutes > 0 ? ` ${remainingMinutes}mins` : '';
    //     return result;
    // }

    function getBackgroundColor(index) {
        let pastelColors = [
            "bg-purple-600",
            "bg-violet-500",
            "bg-indigo-600",
            "bg-blue-600",
            "bg-sky-500",
            "bg-cyan-600",
            "bg-emerald-600",
            "bg-green-500",
            "bg-lime-500",
            "bg-yellow-400",
            "bg-orange-400",
            "bg-orange-600",
            "bg-rose-500",
            "bg-rose-600",
            "bg-pink-700",
        ];

        return pastelColors[index];
    }

    function getWidth(service_code) {
        let total_minutes = 0;
        staffer.services.forEach((service) => {
            if (service.code == service_code) {
                total_minutes = parseFloat(service.total_session_minutes) / 60;
            }
        });

        if (kpi > actual) {
            return (total_minutes / kpi) * 100;
        } else {
            return (total_minutes / actual) * 100;
        }
    }
</script>

<div class="flex justify-between items-center mb-1">
    <h1 class="text-black text-1xl">
        <span class="font-bold">{staffer.name}:</span>
        {@html convertDecimalHoursToHoursAndMinutes(actual)}
    </h1>
    <div>
        <ExpandIndicator
            expanded={staffer.isExpanded}
            class="h-5 w-5 flex-none text-gray-400 shrink-0"
        />
    </div>
</div>
<div class="flex w-full h-6 bg-gray-300">
    <div
        in:slide|global={{ duration: 500, axis: "x" }}
        class="bg-gray-800 h-full"
        style="width: {kpi_width}%"
    ></div>
    <div class="absolute text-white px-1">
        KPI {@html convertDecimalHoursToHoursAndMinutes(kpi)}
    </div>
</div>

<div class="flex w-full h-6 bg-gray-200">
    {#each staffer.services as service, index (service.code)}
        <div
            in:slide|global={{ duration: 1000, axis: "x" }}
            class="{getBackgroundColor(
                index,
            )} h-full text-white text-xs items-center flex px-2"
            style="width: {getWidth(service.code)}%"
        ></div>
    {/each}
</div>
<div
    class="flex text-xs gap-x-2 mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5"
>
    {#each staffer.services as service, index}
        <div class="flex px-1 items-center gap-x-1">
            <span
                class="{getBackgroundColor(
                    index,
                )} flex h-3 w-3 flex-shrink-0 inline-block"
            ></span>{service.code}:
            <span
                >{@html convertDecimalHoursToHoursAndMinutes(
                    parseFloat(service.total_session_minutes) / 60,
                )}</span
            >
        </div>
    {/each}
</div>
