<script>
  import Container from "@shared/Container.svelte";
  import { BreadcrumbStore, RolesStore } from "@shared/stores.js";
  import { push, pop } from "svelte-spa-router";
  import { jspa } from "@shared/jspa.js";
  import { toastError, toastSuccess } from "@shared/toastHelper.js";
  import TripForm from "./TripForm.svelte";
  import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";
  import { haveCommonElements } from "@shared/utilities.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

  export let params;

  let trip_id = params.trip_id;
  let trip = {};
  let stored_trip = {};

  BreadcrumbStore.set({
    path: [{ url: "/trips", name: "Trip Tracker" }],
  });

  getTrip(trip_id);

  function undo() {
    trip = Object.assign({}, stored_trip);
  }

  function getTrip(trip_id) {
    jspa("/Trip", "getTrip", { id: trip_id })
      .then((result) => {
        // trip = result.result;

        const convertedData = convertFieldsToBoolean(result.result, [
          "do_not_bill",
        ]);

        trip = convertedData;
        console.log(trip);
        trip.kms = parseFloat(trip.kms).toFixed(0);
      })
      .catch((error) => {
        let error_message = error.error_message;
        toastError(error_message);
      })
      .finally(() => {
        // Make a copy of the object
        stored_trip = Object.assign({}, trip);
      });
  }

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

  function update(trip) {
    if (!validateTrip(trip)) return;

    if (trip.do_not_bill) trip.client_id = null;

    jspa("/Trip", "updateTrip", trip)
      .then((result) => {
        // trip = result.result;
        toastSuccess("Travel updated");

        if (haveCommonElements($RolesStore, ["accounts", "admin"])) {
          push(`/trips/history/${trip.staff_id}`);
        } else {
          push(`/trips/history`);
        }
      })
      .catch((error) => {
        let error_message = error.error_message;
        toastError(error_message);
      });
  }

  function deleteTrip(trip_id) {
    console.log("deleting trip", trip_id);

    jspa("/Trip", "deleteTrip", { id: trip_id })
      .then((result) => {
        toastSuccess("Travel deleted");
        push(`/trips/history`);
      })
      .catch((error) => {
        let error_message = error.error_message;
        toastError(error_message);
      });
  }

  $: {
    // trip.support_item_number != null || trip.do_not_bill == true
    let show = !(JSON.stringify(trip) === JSON.stringify(stored_trip));

    // if (mounted) {
    ActionBarStore.set({
      can_delete: true,
      show: true,
      undo: () => undo(),
      save: () => update(trip),
      delete: () => deleteTrip(trip_id),
    });
    // }
  }
</script>

<Container>
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Edit Provider Travel
  </div>
  <TripForm bind:trip />
</Container>
