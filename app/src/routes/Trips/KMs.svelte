<script>
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import {
        formatDate,
        getMonday,
        getDatePlusDays,
    } from "@shared/utilities.js";
    import { slide, fade } from "svelte/transition";
    import { quintOut } from "svelte/easing";

    let start_date = getMonday();
    let end_date = getDatePlusDays(start_date, 7);
    let shifts = [];

    BreadcrumbStore.set({
        path: [
            { url: "/trips", name: "Trips" },
            { url: null, name: "KMs" },
        ],
    });

    $: {
        if (start_date && end_date) {
            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;

            if (!dateRegex.test(start_date) || !dateRegex.test(end_date)) {
                // Display an error or handle the invalid date format here
                console.log("Invalid date format");
            } else {
                jspa("/SIL/House/Form", "getKMs", {
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
    </div>
</Container>

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
            <div>Participant</div>
            <div>Staff</div>
        </div>
        <div class="text-right">KMs</div>
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
                    {formatDate(item.date)} ({item.start_shift} to {item.end_shift})
                </div>

                <div class="">{item.participant_name}</div>
                <div class="">{item.staff_name}</div>
            </div>
            <div class="text-right">{item.kilometers_travelled}</div>
        </li>
    {/each}
</ul>
