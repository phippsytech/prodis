<script>
    import { createEventDispatcher } from "svelte";
    import { writable } from "svelte/store";
    import { formatDate } from "@shared/utilities.js";
    import { jspa } from "@shared/jspa.js";

    const PasteStore = writable({});
    const dispatch = createEventDispatcher();

    $: paste = $PasteStore;

    export let client_id;
    export let week_start_date;
    export let shifts = [];

    let can_paste = false;
    let can_copy = false;

    function copyRosterData() {
        const currentWeekEvents = shifts.filter((event) => {
            const eventStart = event.start;
            return eventStart >= week_start_date;
        });

        const startingShifts = currentWeekEvents.map((event) => {
            const id = event.id;
            return { id };
        });

        PasteStore.set({
            client_id: client_id,
            from_date: week_start_date,
            shifts: startingShifts,
        });
    }

    function pasteRosterData() {
        let data = {
            client_id: paste.client_id,
            from_date: paste.from_date,
            to_date: week_start_date,
        };
        jspa("/SIL/House/Roster", "pasteShifts", data)
            .then((result) => {
                dispatch("paste", {});
            })
            .catch((error) => {});
    }

    function cancelPaste() {
        PasteStore.set({});
    }

    function getStartingShifts(shifts) {
        const weekStart = new Date(week_start_date);
        const weekEnd = new Date(week_start_date);
        weekEnd.setDate(weekEnd.getDate() + 7);

        const currentWeekEvents = shifts.filter((event) => {
            const eventStart = new Date(event.start_date);
            return (
                eventStart.getTime() >= weekStart.getTime() &&
                eventStart.getTime() < weekEnd.getTime()
            );
        });

        const startingShifts = currentWeekEvents.map((event) => {
            const id = event.id;
            return { id };
        });

        return startingShifts;
    }

    $: starting_shifts = getStartingShifts(shifts);

    $: can_paste =
        starting_shifts.length === 0 && paste.shifts && paste.shifts.length > 0;
    $: can_copy = starting_shifts.length > 0;
</script>

{#if can_copy || can_paste}
    <div
        class="rounded-md bg-indigo-50 py-2 px-4 mb-2 border border-indigo-200"
    >
        <div class="flex">
            <div class="flex-1 md:flex md:justify-between">
                {#if paste.shifts && paste.shifts.length > 0}
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-indigo-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"
                                ></path>
                            </svg>
                        </div>

                        <p class="ml-3 text-sm text-indigo-600">
                            <span class="font-medium"
                                >You have shifts copied from the week starting {formatDate(
                                    paste.from_date,
                                )}</span
                            ><br />
                            {#if can_paste}
                                <span
                                    >You can paste the shifts into this week.</span
                                >
                            {:else}
                                <span
                                    >To paste, go to a week that has no shifts
                                    starting in it.</span
                                >
                            {/if}
                        </p>
                    </div>
                {:else}
                    <span></span>
                {/if}

                <p class="text-sm md:ml-6 flex items-center">
                    {#if paste.shifts && paste.shifts.length > 0}
                        {#if can_paste}
                            <button
                                on:click={pasteRosterData}
                                type="button"
                                class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500"
                                >Paste</button
                            >
                        {/if}

                        <button
                            on:click={cancelPaste}
                            type="button"
                            class="inline-flex rounded-md bg-indigo-50 p-1.5 text-indigo-600 hover:bg-indigo-100"
                        >
                            <span class="sr-only">Dismiss</span>
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                                />
                            </svg>
                        </button>
                    {:else}
                        <button
                            on:click={copyRosterData}
                            type="button"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-indigo-500"
                            >Copy</button
                        >
                    {/if}
                </p>
            </div>
        </div>
    </div>
{/if}
