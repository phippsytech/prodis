<script>
  import ServiceForm from "./ServiceBookingForm.svelte";
  import ServiceBooking from "./ServiceBooking.svelte";
  import Role from "@shared/Role.svelte";
  import { slide } from "svelte/transition";
  import { haveCommonElements } from "@shared/utilities.js";
  import { ModalStore } from "@app/Overlays/stores";
  import { onMount } from "svelte";
  import { RolesStore } from "@shared/stores.js";
  import { toastError, toastSuccess } from "@shared/toastHelper";
  import createStore from "@shared/createStore";

  export let service_agreement;

  const participant_id = service_agreement.client_id;

  $: roles = $RolesStore;

  let service_booking = {
    client_id: participant_id,
    service_agreement_id: service_agreement.id,
    participant_id: participant_id,
  };

  let ServiceBookingsStore = createStore("/Participant/ServiceBooking", {
    list: "listServiceBookings",
    add: "addServiceBooking",
    update: "updateServiceBooking",
    delete: "deleteServiceBooking",
  });

  onMount(() => {
    ServiceBookingsStore.load({
      service_agreement_id: service_agreement.id,
    });
  });

  function validateField(field, errorMessage) {
    if (field == null || field == "") {
      return null;
    } else if (isNaN(parseFloat(field))) {
      toastError(errorMessage);
      return false;
    }
    return field;
  }

  function validateServiceBooking() {
    service_booking.service_id = validateField(
      service_booking.service_id,
      "Please select a service."
    );

    service_booking.budget = validateField(
      service_booking.budget,
      "Please enter a valid number for the budget."
    );
    if (service_booking.budget === false) return false;

    service_booking.kilometer_budget = validateField(
      service_booking.kilometer_budget,
      "Please enter a valid number for the kilometer budget."
    );
    if (service_booking.kilometer_budget === false) return false;

    service_booking.max_claimable_travel_duration = validateField(
      service_booking.max_claimable_travel_duration,
      "Please enter a valid number for the maximum claimable travel duration."
    );
    if (service_booking.max_claimable_travel_duration === false) return false;
  }

  function showServiceBooking() {
    let service_booking = {
      plan_id: service_agreement.id,
      client_id: participant_id,
      participant_id: participant_id,
      mode: "add",
      budget_start_date: service_agreement.service_agreement_signed_date,
    };

    const stored_service_booking = Object.assign({}, service_booking);

    ModalStore.set({
      label: "Add Service",
      show: true,
      props: stored_service_booking,
      component: ServiceForm,
      action_label: "Add",
      action: () => addServiceBooking(stored_service_booking),
    });
  }

  function editServiceBooking(service_booking) {
    if (haveCommonElements(roles, ["serviceagreement.modify"])) {
      service_booking.mode = "update";

      const stored_service_booking = Object.assign({}, service_booking);

      ModalStore.set({
        label: "Update Service",
        show: true,
        props: stored_service_booking,
        component: ServiceForm,
        action_label: "Update",
        action: () => updateServiceBooking(stored_service_booking),
        delete: () => deleteServiceBooking(stored_service_booking),
      });
    }
  }

  function addServiceBooking(service_booking) {
    if (validateServiceBooking() == false) {
      return;
    }
    ServiceBookingsStore.add(service_booking, true);
  }

  function updateServiceBooking(service_booking) {
    if (validateServiceBooking() == false) {
      return;
    }

    ServiceBookingsStore.updateItem(service_booking, true).then(() => {
      toastSuccess("Service updated successfully.");
    });
  }

  function deleteServiceBooking(service_booking) {
    ServiceBookingsStore.remove(service_booking);
  }
</script>

{#each $ServiceBookingsStore as service_booking, index}
  <li
    in:slide={{ duration: 200 }}
    out:slide={{ duration: 200 }}
    class="px-3 py-2 w-full rounded-t-lg {$ServiceBookingsStore.length - 1 ==
    index
      ? ''
      : 'border-b border-slate-200'}
            
            {service_agreement.is_active
      ? 'hover:bg-indigo-50/50'
      : 'opacity-25'}
            "
  >
    <Role roles={["serviceagreement.modify"]} conditional={true}>
      <div slot="authorised">
        {#if service_agreement.is_active}
          <button
            class="w-full text-left text-slate-400 cursor-pointer text-sm"
            on:click={() => editServiceBooking(service_booking)}
          >
            <ServiceBooking bind:service_booking />
          </button>
        {:else}
          <ServiceBooking bind:service_booking />
        {/if}
      </div>
      <div slot="declined">
        <ServiceBooking bind:service_booking />
      </div>
    </Role>
  </li>
{:else}
  {#if service_agreement.is_active}
    <Role roles={["serviceagreement.modify"]}>
      <li
        class="px-3 py-2 border-t border-indigo-100 w-full text-slate-400 cursor-default text-sm"
      >
        No service bookings have been added to this agreement. &nbsp;<button
          on:click={() => showServiceBooking()}
          class="text-indigo-600 cursor-pointer hover:underline"
          >Add a Service</button
        >
      </li>
    </Role>
  {/if}
{/each}

{#if $ServiceBookingsStore.length > 0 && service_agreement.is_active}
  <Role roles={["serviceagreement.modify"]}>
    <li
      class="px-3 py-2 border-t border-indigo-100 w-full text-slate-400 cursor-default text-sm"
    >
      <button
        on:click={() => showServiceBooking()}
        class="text-indigo-600 cursor-pointer hover:underline"
        >Add another Service</button
      >
    </li>
  </Role>
{/if}
