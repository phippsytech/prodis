<script>
  import AddServiceBookingForm from "./AddServiceBookingForm.svelte";
  import Role from "@shared/Role.svelte";
  import { slide } from "svelte/transition";
  import { haveCommonElements } from "@shared/utilities.js";
  import { ModalStore } from "@app/Overlays/stores";
  import { onMount } from "svelte";
  import { RolesStore } from "@shared/stores.js";
  import { toastError, toastSuccess } from "@shared/toastHelper";

  import { writable } from "svelte/store";

  export let service_agreement;

  const participant_id = service_agreement.client_id;

  $: roles = $RolesStore;

  let service_booking = {
    client_id: participant_id,
    service_agreement_id: service_agreement.id,
    participant_id: participant_id,
  };

  let DraftServiceBookingsStore = createStore(
    "/Participant/DraftServiceBooking",
    {
      list: "listDraftServiceBookings",
      add: "addDraftServiceBooking",
      update: "updateDraftServiceBooking",
      delete: "deleteDraftServiceBooking",
    }
  );

  onMount(() => {
    DraftServiceBookingsStore.load({
      service_agreement_id: service_agreement.id,
    });
  });

  // re load data when service agreement end date is changed
  $: if (service_agreement?.service_agreement_end_date) {
    DraftServiceBookingsStore.load({
      service_agreement_id: service_agreement.id,
    });
  }

  function validateField(field, errorMessage) {
    if (field == null || field == "") {
      return null;
    } else if (isNaN(parseFloat(field))) {
      toastError(errorMessage);
      return false;
    }
    return field;
  }

  function validateDraftServiceBooking() {
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

  function showDraftServiceBooking() {
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
      component: EditDraftServiceBookingForm,
      action_label: "Add",
      action: () => addDraftServiceBooking(stored_service_booking),
    });
  }

  function editDraftServiceBooking(service_booking) {
    if (haveCommonElements(roles, ["serviceagreement.modify"])) {
      service_booking.mode = "update";

      const stored_service_booking = Object.assign({}, service_booking);

      ModalStore.set({
        label: "Update Service",
        show: true,
        props: stored_service_booking,
        component: EditDraftServiceBookingForm,
        action_label: "Update",
        action: () => updateDraftServiceBooking(stored_service_booking),
        delete: () => deleteDraftServiceBooking(stored_service_booking),
      });
    }
  }

  function addDraftServiceBooking(service_booking) {
    if (validateDraftServiceBooking() == false) {
      return;
    }
    DraftServiceBookingsStore.add(service_booking, true);
  }

  function updateDraftServiceBooking(service_booking) {
    if (validateDraftServiceBooking() == false) {
      return;
    }

    DraftServiceBookingsStore.updateItem(service_booking, true).then(() => {
      toastSuccess("Service updated successfully.");
    });
  }

  function deleteDraftServiceBooking(service_booking) {
    DraftServiceBookingsStore.remove(service_booking);
  }
</script>

<li
  class="bg-white flex justify-between items-center px-2 py-2 border-b border-indigo-100 w-full gap-x-2"
>
  <div class="flex-grow">
    <div
      class="grid grid-cols-12 gap-x-2 items-center w-full text-xs text-slate-400 font-semibold"
    >
      <div class="col-span-2 px-2">
        Service Name
        <span class="text-xs">(Rate)</span>
      </div>
      <div class="col-span-2 px-2">Budget</div>
      <div class="col-span-2 px-2">
        KM Budget <span class="text-xs">: Cap</span>
      </div>

      <div class="col-span-6 px-2">Payer</div>
    </div>
  </div>

  <div class="flex-shrink">
    <a class="flex p-1">
      <div class="h-4 w-4 inline m-0 p-0" />
    </a>
  </div>
</li>

{#each addedRows as row, index (index)}
  <li
    class="bg-white group flex justify-between items-center hover:bg-indigo-50 px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
    gap-x-2"
  >
    <div class="flex-grow">
      <div class="grid grid-cols-12 gap-x-2 items-center w-full">
        <div class="col-span-2 px-2">
          {row.service_name}
          <span class="text-xs text-gray-400">({formatCurrency(row.rate)})</span
          >
        </div>
        <div class="col-span-2 px-2">
          {formatCurrency(row.budget)}
        </div>
        <div class="col-span-2 px-2">
          {#if row.km_budget != null}{formatCurrency(row.km_budget)}{/if}
          <span class="text-sm text-gray-400">
            {#if row.km_budget != null}
              : 30m{:else}&mdash;{/if}</span
          >
        </div>

        <div class="col-span-6 px-2">
          {row.plan_manager_name}
        </div>
      </div>
    </div>

    <div class="flex-shrink">
      <a
        class="flex p-1 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300 cursor-pointer"
        on:click={() => removeRow(row)}
      >
        <XMarkIcon class="h-4 w-4 inline m-0 p-0" />
      </a>
    </div>
  </li>
{:else}
  <li
    class=" px-3 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default text-sm"
  >
    No Services have been added to this draft agreement yet.
  </li>
{/each}
<li
  class="bg-white group flex justify-between items-center px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
    gap-x-2"
>
  <Role roles={["admin"]}>
    <button
      class="text-xs text-indigo-600 hover:underline text-left mx-2"
      on:click={() => (displayServiceAgreement = true)}
    >
      <PlusIcon class="w-4 h-4 inline" /> Add Service
    </button>
  </Role>
</li>

{#each $DraftServiceBookingsStore as service_booking, index}
  <li
    in:slide={{ duration: 200 }}
    out:slide={{ duration: 200 }}
    class="px-3 py-2 w-full rounded-t-lg {$DraftServiceBookingsStore.length -
      1 ==
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
            on:click={() => editDraftServiceBooking(service_booking)}
          >
            <DraftServiceBooking bind:service_booking />
          </button>
        {:else}
          <DraftServiceBooking bind:service_booking />
        {/if}
      </div>
      <div slot="declined">
        <DraftServiceBooking bind:service_booking />
      </div>
    </Role>
  </li>
{:else}
  {#if service_agreement.is_active}
    <Role roles={["serviceagreement.modify"]}>
      <li
        class="px-3 py-2 border-t border-indigo-100 w-full text-slate-400 cursor-default text-sm"
      >
        No service bookings have been added to this agreement.
      </li>
    </Role>
  {/if}
{/each}
