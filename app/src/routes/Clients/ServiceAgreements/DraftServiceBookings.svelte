<script>
  import AddDraftServiceBookingForm from "./ServiceBookings/AddServiceBookingForm.svelte";
  import EditDraftServiceBookingForm from "./ServiceBookings/EditServiceBookingForm.svelte";
  import Role from "@shared/Role.svelte";
  import { slide } from "svelte/transition";
  import { formatCurrency, haveCommonElements } from "@shared/utilities.js";
  import { ModalStore } from "@app/Overlays/stores";
  import { onMount } from "svelte";
  import { RolesStore } from "@shared/stores.js";
  import { toastError, toastSuccess } from "@shared/toastHelper";
  import { XMarkIcon } from "heroicons-svelte/24/outline";
  import createStore from "@shared/createStore";

  import { writable, derived } from "svelte/store";

  export let ServiceAgreementStore;
  export let serviceBookingCount = 0;

  $: serviceBookingCount = $DraftServiceBookingsStore.length;

  const draftAgreement = derived(
    ServiceAgreementStore,
    ($ServiceAgreementStore) => {
      return $ServiceAgreementStore.find((agreement) => agreement.is_draft);
    }
  );

  const participant_id = $draftAgreement.client_id;

  $: roles = $RolesStore;

  let service_booking = {
    client_id: participant_id,
    service_agreement_id: $draftAgreement.id,
    participant_id: participant_id,
  };

  let DraftServiceBookingsStore = createStore("/Participant/ServiceBooking", {
    list: "listServiceBookings",
    add: "addServiceBooking",
    update: "updateServiceBooking",
    delete: "deleteServiceBooking",
  });

  onMount(() => {
    DraftServiceBookingsStore.load({
      service_agreement_id: $draftAgreement.id,
    });
  });

  // reload data when service agreement end date is changed
  $: if ($draftAgreement?.service_agreement_end_date) {
    DraftServiceBookingsStore.load({
      service_agreement_id: $draftAgreement.id,
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
      plan_id: $draftAgreement.id,
      client_id: participant_id,
      participant_id: participant_id,
      mode: "add",
      // budget_start_date: $draftAgreement.service_agreement_signed_date,
    };

    const stored_service_booking = Object.assign({}, service_booking);

    ModalStore.set({
      label: "Add Service",
      show: true,
      props: stored_service_booking,
      component: AddDraftServiceBookingForm,
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
        component: AddDraftServiceBookingForm,
        action_label: "Update",
        action: () => updateDraftServiceBooking(stored_service_booking),
        delete: () => deleteDraftServiceBooking(stored_service_booking),
      });
    }
  }

  function addDraftServiceBooking(service_booking) {
    console.log(service_booking);
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
  class="bg-white flex justify-between items-center px-2 py-1 border-b border-indigo-100 w-full gap-x-2"
>
  <div class="flex-grow">
    <div
      class="grid grid-cols-12 gap-x-2 items-center w-full text-xs text-slate-400"
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
    <span class="flex p-1">
      <div class="h-4 w-4 inline m-0 p-0" />
    </span>
  </div>
</li>

{#each $DraftServiceBookingsStore as row, index (index)}
  <li
    class="bg-white group flex justify-between items-center hover:bg-indigo-50 px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
      gap-x-2 cursor-pointer hover:text-indigo-600"
    on:click={() => editDraftServiceBooking(row)}
  >
    <div class="flex-grow">
      <div class="grid grid-cols-12 gap-x-2 items-center w-full">
        <div class="col-span-2 px-2">
          {row.code}
          <span class="text-xs text-gray-400">({formatCurrency(row.rate)})</span
          >
        </div>
        <div class="col-span-2 px-2">
          {formatCurrency(row.allocated_funding)}
        </div>
        
        <div class="col-span-2 px-2">
          {#if row.include_travel}
          {#if row.kilometer_budget != null}{formatCurrency(
              row.kilometer_budget
            )}{/if}
          <span class="text-sm text-gray-400">
            {#if row.kilometer_budget != null}
              : 30m{:else}&mdash;{/if}</span
          >{:else}
          &mdash;
          {/if}
        </div>
        

        <div class="col-span-6 px-2">
          {row.plan_manager_name}
        </div>
      </div>
    </div>

    <div class="flex-shrink">
      <a
        class="flex p-1 text-center rounded-full text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300 cursor-pointer"
        on:click={() => deleteDraftServiceBooking(row)}
      >
        <!-- <XMarkIcon class="h-4 w-4 inline m-0 p-0" /> -->
      </a>
    </div>
  </li>
{:else}
  <li
    class=" px-3 py-2 border-b border-gray-200 w-full text-gray-400 cursor-default text-sm"
  >
    No Services have been added to this draft agreement yet.
  </li>
{/each}
<li
  class="bg-white group flex justify-center items-center px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
      gap-x-2"
>
  <Role roles={["admin"]}>
    <button
      class="text-xs text-indigo-600 hover:underline text-left mx-2"
      on:click={() => showDraftServiceBooking()}
    >
      Add Service
    </button>
  </Role>
</li>
