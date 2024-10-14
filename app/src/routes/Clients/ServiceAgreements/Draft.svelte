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

  import { haveCommonElements } from "@shared/utilities.js";
  import { ModalStore } from "@app/Overlays/stores";
  import { onMount } from "svelte";
  import { RolesStore } from "@shared/stores.js";
  import { toastError, toastSuccess } from "@shared/toastHelper";

  import { writable, derived } from "svelte/store";

  export let service_agreement;

  export let ServiceAgreementStore;

  $: console.log(ServiceAgreementStore);

  $: roles = $RolesStore;

  export let displayServiceAgreement = false;

  export let props = {};

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

  const DraftServiceBookingsStore = createStore(
    "/Participant/DraftServiceBooking",
    {
      list: "listDraftServiceBookings",
      add: "addDraftServiceBooking",
      update: "updateDraftServiceBooking",
      delete: "deleteDraftServiceBooking",
    }
  );

  const draftAgreement = derived(
    ServiceAgreementStore,
    ($ServiceAgreementStore) => {
      return $ServiceAgreementStore.filter((agreement) => agreement.is_draft);
    }
  );

  // const ServiceAgreementStore = createStore(
  //   "/Participant/ServiceAgreement",
  //   {
  //     list: "listServiceAgreementsByParticipantId",
  //     add: "addServiceAgreement",
  //     update: "updateServiceAgreement",
  //     delete: "deleteServiceAgreement",
  //   }
  // );

  // $: $ServiceAgreementStore,
  //   ServiceAgreementStore.set(
  //     $ServiceAgreementStore
  //       .slice()
  //       .sort((a, b) =>
  //         a.is_active === b.is_active ? 0 : a.is_active ? -1 : 1
  //       )
  //   );

  function addDraftServiceAgreement() {
    ServiceAgreementStore.add({
      client_id: participant_id,
      is_draft: true,
      is_active: false,
    });
  }

  //   onMount(() => {
  //     DraftServiceBookingsStore.load({
  //       service_agreement_id: service_agreement.id,
  //     });
  //   });

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

  function cancelDraftServiceAgreement(id) {
    ServiceAgreementStore.remove({ id: id });
  }
</script>

{#if !draftAgreement}
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

    {#each $DraftServiceBookingsStore as row, index (index)}
      <li
        class="bg-white group flex justify-between items-center hover:bg-indigo-50 px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
      gap-x-2"
      >
        <div class="flex-grow">
          <div class="grid grid-cols-12 gap-x-2 items-center w-full">
            <div class="col-span-2 px-2">
              {row.service_name}
              <span class="text-xs text-gray-400"
                >({formatCurrency(row.rate)})</span
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
            class="flex p-1 text-center rounded-full text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300 cursor-pointer"
            on:click={() => removeRow(row)}
          >
            <XMarkIcon class="h-4 w-4 inline m-0 p-0" />
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
    <li
      class="bg-white group flex justify-between items-center px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 w-full
    gap-x-2"
    >
      <div
        class="w-full flex justify-between gap-x-2
    "
      >
        <button
          on:click={() => cancelDraftServiceAgreement(draftAgreement.id)}
          type="button"
          class="w-full sm:w-auto sm:mr-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
          >Cancel</button
        >
        <button
          on:click={() => addService()}
          type="button"
          class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
          >Generate</button
        >
      </div>
    </li>
  </ul>
{/if}
