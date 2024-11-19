<script>
  import BillableForm from "./BillableForm.svelte";
  import { push } from "svelte-spa-router";
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { StafferStore, BreadcrumbStore } from "@shared/stores.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { getClient } from "@shared/api.js";

  export let params;
  let client_id = params.client_id;
  let client = {};
  let timetracking = {};
  let date = new Date();
  let available_session_duration = null;
  let budget_exceeded = false;

  timetracking.staff_id = $StafferStore.id;
  timetracking.client_id = client_id;

  timetracking.session_date = date.toISOString().slice(0, 10);

  BreadcrumbStore.set({
    path: [{ url: null, name: "Billables" }],
  });

  onMount(async () => {
    client = await getClient(client_id);

    BreadcrumbStore.set({
      path: [
        { url: "/clients", name: "Clients" },
        { url: "/clients/" + client.id, name: client.name },
        { name: "Billables" },
      ],
    });
  });

  function addTimeTracking() {
    jspa("/TimeTracking", "addTimeTracking", timetracking)
      .then((result) => {
        push("/clients/" + params.client_id + "/billables");
      })
      .catch(() => {});
  }

  $: {
    let show = false;

    if (
      available_session_duration > 0 &&
      !budget_exceeded &&
      timetracking.staff_id &&
      timetracking.client_id &&
      timetracking.service_id
    ) {
      show = true;
    }

    ActionBarStore.set({
      can_delete: false,
      show: show,
      save: () => addTimeTracking(),
    });
  }
</script>

<h2 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Add Billable Item
</h2>
<!-- <div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular " style="color:#220055;">Add Billable Item</div> -->
<BillableForm
  bind:timetracking
  bind:available_session_duration
  bind:budget_exceeded
/>
