<script>
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import {
        getMonday,
        getDatePlus7Days,
        formatDate,
    } from "@shared/utilities.js";
    import { ExclamationTriangleIcon } from "heroicons-svelte/24/outline";
    import StaffSelector from "@shared/StaffSelector.svelte";
    import ClientSelector from "@shared/ClientSelector.svelte";
    import Filter from "@shared/PhippsyTech/svelte-ui/Filter.svelte";

    const date = new Date();

    let trips = [];
    let start_date = getMonday();
    let end_date = getDatePlus7Days(start_date);
    let staff_id = null;
    let client_id = null;
    let trips_list = [];
    let filters = [
		{ label: "Speed < 40Kms/hr", value: "not_acceptable_speed", enabled: false }
	];

    BreadcrumbStore.set({
        path: [
            { url: "/reports", name: "Reports" },
            { url: null, name: "Provider Travel Report" },
        ],
    });

    function getTripsByDate(start_date, end_date, staff_id = null, client_id = null) {
        if (start_date && end_date) {
            const startDateRegex = /^\d{4}-\d{2}-\d{2}$/;
            const endDateRegex = /^\d{4}-\d{2}-\d{2}$/;

            if (
                !startDateRegex.test(start_date) ||
                !endDateRegex.test(end_date)
            ) {
                // Display an error or handle the invalid date format here
            } else {
                trips = [];
                jspa("/Trip", "listTripsByDate", {
                    start_date: start_date,
                    end_date: end_date,
                    staff_id: staff_id,
                    client_id: client_id,
                }).then((result) => {
                    trips = result.result;
                    trips_list = trips;
                    // trips = trips.filter((trip) => trip.staff_id == 100);
                    // trips = result.result.filter(
                    //     (trip) =>
                    //         !isSpeedAcceptable(trip.kms, trip.trip_duration),
                    // );
                    // trips.sort((a, b) => Number(b.kms) - Number(a.kms));
                });
            }
        }
    }

    $: getTripsByDate(start_date, end_date, staff_id, client_id);

    $: {
        const showNotAcceptableSpeed = filters.find(
            (f) => f.value === "not_acceptable_speed",
        ).enabled;
        
        
        if (showNotAcceptableSpeed) {
            trips_list = trips.filter((trip) =>
                !isSpeedAcceptable(trip.kms, trip.trip_duration)
            );
        } else {
            trips_list = trips;
        }
    }

    function displayClaimableKms(trip) {
        // Maximum Claimable Time: Auto-calculation or a display based on zone constraints.
        // Claimable Kilometers: Automatically calculated field based on the entered duration.

        // const maxClaimableTime = service.max_claimable_travel_duration; // Maximum claimable time in minutes

        const maxClaimableTime = 30; // Maximum claimable time in minutes

        let claimableKms = trip.kms;
        let claimableTime = trip.trip_duration; // Use numeric value for time

        if (trip.trip_duration > maxClaimableTime) {
            const claimablePercentage = maxClaimableTime / trip.trip_duration;
            claimableKms =
                Math.round(trip.kms * claimablePercentage * 100) / 100; // Rounded to 2 decimal places
            claimableTime = maxClaimableTime; // Use numeric value for max time
        }

        return `Claimable: ${claimableKms} kms - ${claimableTime} minutes`;
    }

    function isSpeedAcceptable(kms, duration) {
        if (kms > 0 && duration > 0) {
            let speed = kms / (duration / 60);
            if (speed < 40) {
                return false;
            } else {
                return true;
            }
        }
        if (duration > 0) {
            return false;
        }

        return true;
    }
</script>

<div class="bg-slate-100 px-3 pt-2 pb-4 my-2 rounded-md">
    <h1 class="font-fredoka-one-regular text-indigo-900 text-2xl mt-0 mb-2">
        Provider Travel Report
    </h1>

    <div class="text-sm mb-1 text-slate-400">Period</div>

    <div class="flex flex-wrap space-x-2 items-center md:flex-no-wrap">
        <FloatingDate label="Start Date" bind:value={start_date} />
        <FloatingDate label="End Date" bind:value={end_date} />
        <StaffSelector bind:staff_id={staff_id} clearable />
        <ClientSelector bind:client_id={client_id} bind:staff_id={staff_id} clearable />
    </div>
</div>

<div class="bg-white px-3 rounded-md pb-1 my-2">
    <Filter bind:filters />
</div>

<Container>
    <ul class="list-none m-0 p-0 divide-y divide-slate-200">
        {#each trips_list as trip (trip.id)}
            <a href="/#/trips/{trip.id}">
                <li
                    class="flex justify-between items-center px-4 py-2 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer"
                >
                    <div class="text-sm font-medium">
                        <div class="font-semibold">
                            {formatDate(trip.trip_date)} -
                            <span
                                class={trip.client_id
                                    ? ""
                                    : "font-italic text-slate-500"}
                            >
                                {trip.staff_name}
                            </span>
                            <span class="font-light text-xs"
                                >({trip.vehicle_type.charAt(0).toUpperCase() +
                                    trip.vehicle_type.slice(1).toLowerCase()}
                                Vehicle)</span
                            >
                            <div>
                                {#if trip.client_id}{trip.client_name}{:else}No
                                    Participant{/if}
                            </div>
                        </div>
                        <div>
                            Travelled: {trip.kms} km - {trip.trip_duration}
                            minutes {#if !isSpeedAcceptable(trip.kms, trip.trip_duration)}
                                <ExclamationTriangleIcon
                                    class="h-5 w-5 text-red-800 inline-block"
                                />
                                {trip.kms / (trip.trip_duration / 60)}Kms/hr
                            {/if}
                            {#if trip.do_not_bill}
                                <span class="text-slate-500"> - No Bill</span>
                            {/if}
                        </div>
                        <div class="text-xs text-slate-500">
                            {displayClaimableKms(trip)}
                        </div>
                        <div class="text-sm text-slate-700">
                            {#if trip.trip_purpose}{trip.trip_purpose}{/if}
                        </div>
                    </div>
                </li></a
            >
        {:else}
            <div class="text-sm text-center text-slate-500 p-4 pt-2">
                <div
                    class="flex justify-center items-center h-8 w-8 text-slate-300 mx-auto m-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512"
                        fill="currentColor"
                        ><path
                            d="M135.2 117.4L109.1 192H402.9l-26.1-74.6C372.3 104.6 360.2 96 346.6 96H165.4c-13.6 0-25.7 8.6-30.2 21.4zM39.6 196.8L74.8 96.3C88.3 57.8 124.6 32 165.4 32H346.6c40.8 0 77.1 25.8 90.6 64.3l35.2 100.5c23.2 9.6 39.6 32.5 39.6 59.2V400v48c0 17.7-14.3 32-32 32H448c-17.7 0-32-14.3-32-32V400H96v48c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32V400 256c0-26.7 16.4-49.6 39.6-59.2zM128 288a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"
                        /></svg
                    >
                </div>
                <div>No trip data is available for this period</div>
            </div>
        {/each}
    </ul>
</Container>