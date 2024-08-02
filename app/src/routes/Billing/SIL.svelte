<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import {
        formatDate,
        getMonday,
        convertToHoursAndMinutes,
        getDatePlusDays,
    } from "@shared/utilities.js";
    import { slide, fade } from "svelte/transition";
    import { quintOut } from "svelte/easing";
    import BillingPeriodStartDate from "./BillingPeriodStartDate.svelte";

    let start_date = getMonday();
    let end_date = getDatePlusDays(start_date, 7);

    let client_id;
    let shifts = [];

    BreadcrumbStore.set({
        path: [
            { url: "/billing", name: "Billing" },
            { url: null, name: "SIL" },
        ],
    });

    function generateSessions() {
        jspa("/Roster", "generateSessions", {
            client_id: client_id,
            start_date: start_date,
            end_date: end_date,
        }).then((result) => {
            shifts = result.result;
            alert("Sessions generated");
        });
    }

    $: {
        if (start_date && end_date && client_id) {
            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;

            if (!dateRegex.test(start_date) || !dateRegex.test(end_date)) {
                // Display an error or handle the invalid date format here
            } else {
                jspa("/SIL/Billing", "getBilling", {
                    client_id: client_id,
                    start_date: start_date,
                    end_date: end_date,
                }).then((result) => {
                    shifts = result.result;
                });
            }
        }
    }
</script>

<!-- I think overview should be dashboard throughout -->

<Container>
    <BillingPeriodStartDate />

    <p>Just sneaking this in here until I can work out where it should go.</p>

    <div class="text-sm mb-1 font-bold opacity-50">Period</div>
    <div
        class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
    >
        <div class="flex-grow">
            <div
                class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
            >
                <label class="text-xs opacity-50 p-0 m-0 block"
                    >Start Date</label
                >
                <input
                    type="date"
                    class="w-full p-0 m-0 outline-none bg-white"
                    bind:value={start_date}
                />
            </div>
        </div>

        <div class="flex-grow">
            <div
                class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
            >
                <label class="text-xs opacity-50 p-0 m-0 block">End Date</label>
                <input
                    type="date"
                    class="w-full p-0 m-0 outline-none bg-white"
                    bind:value={end_date}
                />
            </div>
        </div>

        <div class="flex-grow">
            <FloatingSelect
                label="SIL Participants"
                bind:value={client_id}
                options={[
                    { option: "Logan", value: 332 },
                    { option: "Rhys", value: 424 },
                    { option: "CJ", value: 580 },
                ]}
            />
        </div>

        <button
            on:click={() => generateSessions()}
            type="button"
            class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 mb-2"
            >Generate</button
        >
    </div>
</Container>

<!-- <Container>
    <div class="text-sm mb-1 font-bold opacity-50">Selected Total</div>
    <p class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:tracking-tight mb-2">$54,639.27</p>    
</Container> -->

<!-- <Container>
    <div class="text-sm mb-1 font-bold opacity-50">Filter</div>
    </Container> -->

{#each shifts as shift}
    <div class=""></div>
{/each}

<div
    in:fade={{ delay: 250, duration: 250, easing: quintOut }}
    out:fade={{ duration: 50, easing: quintOut }}
    class="hidden sm:block"
>
    <div
        class="grid grid-cols-2 items-center py-1 text-xs font-medium text-gray-500"
        style="grid-template-columns: 6fr 1fr;"
    >
        <div class="grid grid-cols-3 gap-4 items-center">
            <div>Start Date</div>
            <div>End Date</div>
            <div>Service</div>
        </div>
        <div class="text-right">Time</div>
    </div>
</div>

<ul class="border-y border-gray-200 w-full mb-4">
    {#each shifts as item, index}
        <li
            in:slide|global={{ duration: 250 }}
            out:slide|global={{ duration: 250 }}
            class="grid grid-cols-2 text-sm border-b border-gray-200 items-center hover:bg-indigo-50 py-1 cursor-pointer"
            style="grid-template-columns: 6fr 1fr; "
        >
            <div
                class="grid grid-cols-1 gap-0 sm:gap-4 sm:grid-cols-3 w-full items-center"
            >
                <div class="font-medium">
                    {formatDate(item.start_date)}
                    {item.start_time}
                </div>
                <div class="whitespace-no-wrap">
                    {formatDate(item.end_date)}
                    {item.end_time}
                </div>
                <div class="">{item.service_code}</div>
            </div>
            <div class="text-right">
                {convertToHoursAndMinutes(item.session_duration / 60)}
            </div>
        </li>
    {/each}
</ul>
