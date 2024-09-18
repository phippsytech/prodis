<script>
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";

    import TripForm from "./TripForm.svelte";

    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    let history;
    let tripForm; // This is the TripForm component

    const INITIAL_TRIP_STATE = {
        client_id: null,
        staff_id: $StafferStore.id,
        trip_date: new Date().toISOString().split("T")[0],
        vehicle_type: "private",
        kms: null,
        do_not_bill: false,
        // service_id: null,
        service_booking_id: null,
        planmanager_id: null,
        max_claimable_travel_duration: null,
        trip_purpose: null,
        trip_duration: 0,
    };

    let trip = { ...INITIAL_TRIP_STATE };

    BreadcrumbStore.set({
        path: [{ url: null, name: "Trip Tracker" }],
    });

    function validateTrip(trip) {
        if (!trip.do_not_bill && !trip.client_id) {
            toastError("Please select a participant");
            return false;
        }
        if (!trip.kms || isNaN(trip.kms)) {
            toastError("Please enter a valid number of KMs");
            return false;
        }
        if (!trip.trip_purpose) {
            toastError("Please select or type a travel purpose.");
            return false;
        }
        return true;
    }

    function add(trip) {
        if (!validateTrip(trip)) return;

        if (trip.do_not_bill) trip.client_id = null;

        jspa("/Trip", "addTrip", trip)
            .then((result) => {
                // trip = result.result;
                tripForm.resetTrip();
                toastSuccess("Travel added");

                history.listTrips();
            })
            .catch((error) => {
                // let error_message = error.error_message;
                // toastError(error_message);
            });
    }

    let show = false;
    $: {
        if (trip.service_booking_id != null || trip.do_not_bill == true) {
            show = true;
        } else {
            show = false;
        }

        ActionBarStore.set({
            can_delete: false,
            show: show,
            save: () => add(trip),
        });
    }
</script>

<Container>
    <div
        class="text-2xl tracking-tight font-fredoka-one-regular mb-2 text-indigo-900"
    >
        Add Travel
    </div>
    <TripForm bind:this={tripForm} bind:trip />
</Container>
