<script>
  import ServiceCombo from "@app/routes/Service/ServiceCombo.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import InlineDate from "@shared/PhippsyTech/svelte-ui/forms/InlineDate.svelte";
  import PlanManagerCombo from "@app/routes/Accounts/PlanManagers/PlanManagerCombo.svelte";
  import Container from "@shared/Container.svelte";
  import { slide } from "svelte/transition";
  import createStore from "@shared/createStore";

  import Role from "@shared/Role.svelte";

  import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
  import {
    PlusIcon,
    XMarkIcon,
    CloudArrowUpIcon,
    EnvelopeIcon,
    ArchiveBoxIcon,
    DocumentTextIcon,
    DocumentArrowUpIcon,
    TrashIcon,
    UserIcon,
  } from "heroicons-svelte/24/outline";

  import FileSignatureIcon from "@shared/PhippsyTech/svelte-ui/icons/FileSignature.svelte";
  import FileContractIcon from "@shared/PhippsyTech/svelte-ui/icons/FileContract.svelte";

  import FileUploader from "@shared/FileUploader.svelte";
  import { formatCurrency } from "@shared/utilities.js";
  import AddServiceBookingForm from "./ServiceBookings/AddServiceBookingForm.svelte";
  import DraftServiceBookings from "./DraftServiceBookings.svelte";

  import { haveCommonElements } from "@shared/utilities.js";
  import { ModalStore } from "@app/Overlays/stores";
  import { onMount } from "svelte";
  import { RolesStore } from "@shared/stores.js";
  import { toastError, toastSuccess } from "@shared/toastHelper";

  import { writable, derived } from "svelte/store";

  // export let service_agreement;

  export let ServiceAgreementStore;

  $: roles = $RolesStore;

  // export let displayServiceAgreement = false;

  // export let props = {};

  export let participant_id;

  let addedRows = [
    // {
    //   service_name: "SBIS",
    //   rate: 244.22,
    //   budget: 20000,
    //   km_budget: 1000,
    //   plan_manager_name: "National Disability Insurance Agency",
    // },
  ];

  // const DraftServiceBookingsStore = createStore(
  //   "/Participant/DraftServiceBooking",
  //   {
  //     list: "listDraftServiceBookings",
  //     add: "addDraftServiceBooking",
  //     update: "updateDraftServiceBooking",
  //     delete: "deleteDraftServiceBooking",
  //   }
  // );

  const draftAgreement = derived(
    ServiceAgreementStore,
    ($ServiceAgreementStore) => {
      return $ServiceAgreementStore.find((agreement) => agreement.is_draft);
    }
  );

  function addDraftServiceAgreement() {
    ServiceAgreementStore.add({
      client_id: participant_id,
      is_draft: true,
      is_active: false,
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

  // function validateDraftServiceBooking() {
  //   service_booking.service_id = validateField(
  //     service_booking.service_id,
  //     "Please select a service."
  //   );

  //   service_booking.budget = validateField(
  //     service_booking.budget,
  //     "Please enter a valid number for the budget."
  //   );
  //   if (service_booking.budget === false) return false;

  //   service_booking.kilometer_budget = validateField(
  //     service_booking.kilometer_budget,
  //     "Please enter a valid number for the kilometer budget."
  //   );
  //   if (service_booking.kilometer_budget === false) return false;

  //   service_booking.max_claimable_travel_duration = validateField(
  //     service_booking.max_claimable_travel_duration,
  //     "Please enter a valid number for the maximum claimable travel duration."
  //   );
  //   if (service_booking.max_claimable_travel_duration === false) return false;
  // }

  // function addDraftServiceBooking(service_booking) {
  //   if (validateDraftServiceBooking() == false) {
  //     return;
  //   }
  //   DraftServiceBookingsStore.add(service_booking, true);
  // }

  // function updateDraftServiceBooking(service_booking) {
  //   if (validateDraftServiceBooking() == false) {
  //     return;
  //   }

  //   DraftServiceBookingsStore.updateItem(service_booking, true).then(() => {
  //     toastSuccess("Service updated successfully.");
  //   });
  // }

  // function deleteDraftServiceBooking(service_booking) {
  //   DraftServiceBookingsStore.remove(service_booking);
  // }

  function cancelDraftServiceAgreement(draftAgreement) {
    console.log(draftAgreement);
    ServiceAgreementStore.remove(draftAgreement);
  }
</script>

{#if !$draftAgreement}
  <div class="rounded-md text-slate-400 px-4 pb-2 pt-3">
    <button
      class="text-sm text-indigo-600 hover:underline text-left mx-2"
      on:click={() => addDraftServiceAgreement()}
    >
      Draft a new Service Agreement
    </button>
  </div>
{:else}
  <ul class=" text-slate-800">
    <li
      class="group items-center px-2 pt-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500
    border-b border-indigo-100 w-full gap-x-2
    "
    >
      <div class="flex flex-wrap space-x-2 items-center md:flex-no-wrap">
        <FloatingDate
          bind:value={draftAgreement.service_agreement_signed_date}
          label="Start Date"
        />

        <FloatingDate
          bind:value={draftAgreement.service_agreement_end_date}
          label="End Date"
        />
      </div>
    </li>

    <li
      class=" group items-center px-2 pt-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500
    border-b border-indigo-100 w-full gap-x-2
    "
    >
      <div class="flex gap-x-2 flex-wrap">
        <div class="w-full sm:w-2/5 sm:flex-1">
          <FloatingInput
            bind:value={draftAgreement.name}
            label="Participant's Representative (Signatory)"
            placeholder="eg: Chris Person"
          />
        </div>
        <div
          class="flex flex-wrap xs:flex-nowrap gap-2 justify-between w-full sm:w-3/5"
        >
          <div class="flex-grow">
            <FloatingInput
              bind:value={draftAgreement.email}
              label="Email Address"
              placeholder="eg: chris@person.com"
            />
          </div>
          <div class="flex-grow-0">
            <FloatingInput
              bind:value={draftAgreement.phone}
              label="Contact Number"
              placeholder="eg: 04XX XXX XXX"
            />
          </div>
        </div>
      </div>
    </li>

    <DraftServiceBookings {ServiceAgreementStore} />

    <li
      class="bg-white group flex justify-between items-center px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 w-full
    gap-x-2"
    >
      <div
        class="w-full flex justify-between gap-x-2
    "
      >
        <button
          on:click={() => cancelDraftServiceAgreement($draftAgreement)}
          type="button"
          class="w-full sm:w-auto sm:mr-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
          >Cancel</button
        >
        <div>
          <button
            on:click={() => addService()}
            type="button"
            class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
            >Save</button
          >

          <button
            on:click={() => addService()}
            type="button"
            disabled
            title="Generation unavailable until changes are saved"
            class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm shadow-sm
            border-dashed border border-slate-300 bg-white text-slate-300 cursor-not-allowed
            ">Generate</button
          >
        </div>
      </div>
    </li>
  </ul>
{/if}
