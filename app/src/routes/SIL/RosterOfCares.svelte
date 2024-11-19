<script>
  import { push } from "svelte-spa-router";
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { slide } from "svelte/transition";
  import { flip } from "svelte/animate";
  import { quintOut } from "svelte/easing";
  import { haveCommonElements } from "@shared/utilities.js";
  import { StafferStore, RolesStore } from "@shared/stores.js";
  import { BreadcrumbStore } from "@shared/stores.js";

  import RosterOfCareForm from "./RosterOfCare/RosterOfCareForm.svelte";

  import Role from "@shared/Role.svelte";

  import { ModalStore } from "@app/Overlays/stores";

  export let client = {};
  export let client_id;
  export let plan = {};

  $: stafferStore = $StafferStore;
  $: rolesStore = $RolesStore;

  BreadcrumbStore.set({
    path: [{ url: null, name: "RosterOfCares" }],
  });

  let roster_of_cares = [];
  let roster_of_care = {};

  onMount(() => {
    jspa("/SIL/RosterOfCare", "listRosterOfCares", {}).then((result) => {
      roster_of_cares = result.result;
    });
  });

  $: roles = $RolesStore;

  function showRosterOfCare() {
    roster_of_care = {};
    ModalStore.set({
      label: "Add Roster Of Care",
      show: true,
      props: roster_of_care,
      component: RosterOfCareForm,
      action_label: "Add",
      action: () => addRosterOfCare(),
    });
  }

  function editRosterOfCare(roster_of_care_id) {
    jspa("/RosterOfCare", "getRosterOfCare", {
      id: roster_of_care_id,
    }).then((result) => {
      roster_of_care = result.result;

      ModalStore.set({
        label: "Update RosterOfCare",
        show: true,
        props: roster_of_care,
        component: RosterOfCareForm,
        action_label: "Update",
        action: () => updateRosterOfCare(roster_of_care_id),
        // delete: ()=>deleteRosterOfCare(roster_of_care_id)
      });
    });
  }

  function addRosterOfCare() {
    jspa("/RosterOfCare", "addRosterOfCare", roster_of_care).then((result) => {
      if (result.result.id != 0) {
        roster_of_cares.push(result.result);
        roster_of_cares = roster_of_cares;
      }
    });
  }

  function updateRosterOfCare(roster_of_care_id) {
    jspa("/RosterOfCare", "updateRosterOfCare", roster_of_care).then(
      (result) => {
        roster_of_cares.push(result.result);
        roster_of_cares = roster_of_cares;
      }
    );
  }
</script>

<div class="flex items-center mb-2 px-4">
  <div class="flex-auto">
    <div
      class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
      Roster Of Cares
    </div>
    <p>Calculating Supported Independent Living (SIL) claims.</p>
  </div>
  <button
    on:click={() => showRosterOfCare()}
    type="button"
    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
    >Add Roster Of Care</button
  >
</div>

<div
  class="flex grow flex-col gap-y-2 overflow-y-auto border-r border-gray-200 bg-white"
>
  <!-- <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight pt-5 px-5">Staff</h2> -->
  <div
    class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 m-4"
  >
    {#each roster_of_cares as roster_of_care, index (roster_of_care.id)}
      <div
        on:click={() => push("/sil/rosterofcares/" + roster_of_care.id)}
        animate:flip={{
          delay: 10 * index,
          opacity: 0,
          duration: 150,
          easing: quintOut,
        }}
        in:slide|global={{
          duration: 150,
          opacity: 0,
          delay: 10 * index,
        }}
        class="border border-gray-200 rounded rounded-lg relative flex justify-between py-2 px-5 text-gray-700 hover:bg-indigo-600 hover:text-white bg-white cursor-pointer"
      >
        <div>
          <div class="font-medium">
            {roster_of_care.participant_name}
          </div>
          <!-- <div class="text-sm font-medium">
                    {#if roster_of_care.roster_of_care_type=="short"}Short-term{/if}
                    {#if roster_of_care.roster_of_care_type=="long"}Long-term{/if}
                </div>
                <div class="text-xs">{roster_of_care.address}<br/>{roster_of_care.suburb}  {roster_of_care.state}  {roster_of_care.postcode}</div> -->
        </div>
      </div>
    {/each}
  </div>
</div>
