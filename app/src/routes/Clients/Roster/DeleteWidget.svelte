<script>
    import { createEventDispatcher } from "svelte";
    import { jspa } from "@shared/jspa.js";

    const dispatch = createEventDispatcher();

    export let client_id;
    export let week_start_date;

    let weekStartDate;
    let currentDate;

    $: {
        weekStartDate = new Date(week_start_date);
        currentDate = new Date();
    }

    function deleteRosterData() {
        if (
            !confirm(
                "You are about to delete all shifts for this client for the week starting " +
                    week_start_date +
                    ". \n\n Are you sure?",
            )
        ) {
            return;
        }

        let data = {
            client_id: client_id,
            start_date: week_start_date,
        };
        jspa("/SIL/House/Roster", "deleteShifts", data)
            .then((result) => {
                dispatch("delete", {});
            })
            .catch((error) => {});
    }
</script>

{#if weekStartDate > currentDate}
    <button
        on:click={deleteRosterData}
        type="button"
        class="w-full sm:w-auto inline-flex justify-center rounded-md bg-red-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-red-700 hover:font-bold"
        >Delete shifts for entire week</button
    >
{/if}
