<script>
  import { push } from "svelte-spa-router";
  import BillableForm from "./BillableForm.svelte";
  import { jspa } from "@shared/jspa.js";
  import {
    StafferStore,
    BreadcrumbStore,
    SessionIdStore,
  } from "@shared/stores.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

  document.title = "Add Billable";

  let timetracking = {};
  let date = new Date();
  let available_session_duration = null;
  let budget_exceeded = false;

  timetracking.staff_id = $StafferStore.id;
  timetracking.session_date = date.toISOString().slice(0, 10);

  BreadcrumbStore.set({
    path: [{ url: null, name: "Billables" }],
  });

  function addTimeTracking() {
    jspa("/TimeTracking", "addTimeTracking", timetracking)
      .then((result) => {
        let retrievedSessionId = result.result.id;
        SessionIdStore.set(retrievedSessionId);
        push("/billables/unbilled");
      })
      .catch((error) => {});
  }

  $: {
    let show = false;

    if (
      available_session_duration > 0 &&
      !budget_exceeded &&
      timetracking.staff_id &&
      timetracking.client_id &&
      timetracking.service_booking_id
    ) {
      show = true;
    }

    // prevent saving if claim type is cancellation and no reason is selected
    if (
      timetracking.claim_type === "CANC" &&
      timetracking.cancellation_reason === "Choose reason for cancellation"
    ) {
      show = false;
    }

    ActionBarStore.set({
      can_delete: false,
      show: show,
      save: () => addTimeTracking(),
    });
  }
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Add Billable
</div>

<BillableForm
  bind:timetracking
  bind:available_session_duration
  bind:budget_exceeded
/>
