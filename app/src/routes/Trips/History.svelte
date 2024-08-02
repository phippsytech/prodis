<script>
    import { onMount } from "svelte";
    // import Container from "@shared/Container.svelte";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    // import Pager from "@shared/PhippsyTech/svelte-ui/Pager.svelte";
    import { jspa } from "@shared/jspa.js";
    import { formatDate } from "@shared/utilities.js";
    
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";
    import StaffSelector from "@app/routes/Billables/StaffSelector.svelte";
    import Role from "@shared/Role.svelte";
    

    export let params;

    // let params = new URLSearchParams(document.location.search);
    let staff_id =
        params && params.staff_id ? params.staff_id : $StafferStore.id;

    let trips = [];

    BreadcrumbStore.set({
        path: [{ url: null, name: "Trip Tracker" }],
    });

    onMount(() => {
        listTrips();
    });

    export function listTrips() {
        if (staff_id == null) return;
        trips = [];
        jspa("/Trip", "listTrips", { staff_id: staff_id }).then((result) => {
            // trips = result.result;

            trips = convertFieldsToBoolean(result.result, ["do_not_bill"]);
        });
    }

    $: if (staff_id) {
        history.pushState({}, "", `/#/trips/history/${staff_id}`);

        listTrips();
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
</script>

<Role roles={["super"]}>
    <div class="flex-1">
        <StaffSelector bind:staff_id />
    </div>
</Role>
{#if trips.length > 0}
    <div class="font-bold px-4 text-xl mb-2 text-indigo-900">
        Travel History
    </div>

    <ul class="list-none m-0 p-0 divide-y divide-slate-200">
        {#each trips as trip (trip.id)}
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
                                >{#if trip.client_id}{trip.client_name}{:else}No
                                    Participant{/if}</span
                            >
                            <span class="font-light text-xs"
                                >({trip.vehicle_type.charAt(0).toUpperCase() +
                                    trip.vehicle_type.slice(1).toLowerCase()}
                                Vehicle)</span
                            >
                        </div>
                        <div>
                            Travelled: {trip.kms} km - {trip.trip_duration}
                            minutes
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
        {/each}
    </ul>

    <!-- Future improvement!  Paged results! -->
    <!-- <Pager /> -->
{/if}
