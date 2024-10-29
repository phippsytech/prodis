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

  import {
    haveCommonElements,
    normalizeSignedDate,
  } from "@shared/utilities.js";
  import { ModalStore } from "@app/Overlays/stores";
  import { onMount, onDestroy } from "svelte";
  import { RolesStore } from "@shared/stores.js";
  import { toastError, toastSuccess } from "@shared/toastHelper";
  import { writable, derived, get } from "svelte/store";

  export let ServiceAgreementStore;

  let serviceBookingCount;

  $: console.log(serviceBookingCount);

  $: roles = $RolesStore;

  export let participant_id;

  let client = {};

  let addedRows = [];

  // Create a writable store for draftAgreement
  export const draftAgreement = writable(null);
  let storedDraftAgreement = {};

  // Subscribe to ServiceAgreementStore changes
  let unsubscribe = ServiceAgreementStore.subscribe(
    ($ServiceAgreementStore) => {
      const draft = $ServiceAgreementStore.find(
        (agreement) => agreement.is_draft
      );
      if (draft) {
        draftAgreement.set(draft); // Update draftAgreement if a draft is found
        storedDraftAgreement = Object.assign({}, draft);
      } else {
        // If no draft is found (e.g., it was removed), reset or clear draftAgreement
        draftAgreement.set(null); // Set draftAgreement to null or an appropriate value
        storedDraftAgreement = null;
      }
    }
  );

  onDestroy(() => {
    if (unsubscribe) {
      unsubscribe(); // Clean up the subscription
    }
  });

  function addDraftServiceAgreement() {
    let draftAgreement = {
      client_id: participant_id,
      status: "draft",
      is_draft: true,
      is_active: false,
    };
    ServiceAgreementStore.add(draftAgreement, true);
  }

  function saveDraftServiceAgreement(draftAgreement) {
    draftAgreement.service_agreement_signed_date = normalizeSignedDate(
      draftAgreement.service_agreement_signed_date
    );
    draftAgreement.service_agreement_end_date = normalizeSignedDate(
      draftAgreement.service_agreement_end_date
    );
    ServiceAgreementStore.updateItem(draftAgreement).then(() => {
      storedDraftAgreement = Object.assign({}, draftAgreement);
      toastSuccess("Draft Service Agreement saved.");
    });
  }

  function generateServiceAgreement(draftAgreement) {
    draftAgreement.status = "pending";
    draftAgreement.is_draft = false;
    ServiceAgreementStore.updateItem(draftAgreement).then(() => {
      toastSuccess("Service Agreement generated.");
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

  function cancelDraftServiceAgreement(agreement) {
    if (
      confirm("Are you sure you want to cancel this draft service agreement?")
    ) {
      ServiceAgreementStore.remove(agreement);
      draftAgreement.set({});
      console.log($draftAgreement);
    }
  }

  $: canGenerate = validateDraftServiceAgreeement(
    $draftAgreement,
    serviceBookingCount
  );

  function validateDraftServiceAgreeement(draftAgreement, serviceBookingCount) {
    if (
      !(JSON.stringify(draftAgreement) === JSON.stringify(storedDraftAgreement))
    )
      return false;
    if (serviceBookingCount == 0) return false;
    if (!draftAgreement?.service_agreement_signed_date) return false;
    if (!draftAgreement?.service_agreement_end_date) return false;
    if (!draftAgreement?.signatory_email) return false;
    if (!draftAgreement?.signatory_name) return false;
    if (!draftAgreement?.signatory_phone) return false;

    return true;
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
          bind:value={$draftAgreement.service_agreement_signed_date}
          label="Start Date"
        />

        <FloatingDate
          bind:value={$draftAgreement.service_agreement_end_date}
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
            bind:value={$draftAgreement.signatory_name}
            label="Participant's Representative (Signatory)"
            placeholder="eg: Chris Person"
          />
        </div>
        <div
          class="flex flex-wrap xs:flex-nowrap gap-2 justify-between w-full sm:w-3/5"
        >
          <div class="flex-grow">
            <FloatingInput
              bind:value={$draftAgreement.signatory_email}
              label="Email Address"
              placeholder="eg: chris@person.com"
            />
          </div>
          <div class="flex-grow-0">
            <FloatingInput
              bind:value={$draftAgreement.signatory_phone}
              label="Contact Number"
              placeholder="eg: 04XX XXX XXX"
            />
          </div>
        </div>
      </div>
    </li>

    <DraftServiceBookings {ServiceAgreementStore} bind:serviceBookingCount />

    <li>
      {#if !canGenerate}
        <div class="rounded-md bg-yellow-50 p-2 m-2">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-yellow-400"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
                data-slot="icon"
              >
                <path
                  fill-rule="evenodd"
                  d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <!-- <h3 class="text-sm font-medium text-yellow-800">
                Attention needed
              </h3> -->
              <div class=" text-sm text-yellow-700">
                <p>
                  To generate a pending service agreement, fill in all fields,
                  save your changes, and ensure at least one service is added.
                </p>
              </div>
            </div>
          </div>
        </div>
      {/if}
    </li>

    <li
      class=" group flex justify-between items-center px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 w-full
    gap-x-2 bg-gray-50"
    >
      <div
        class="w-full flex justify-between gap-x-2
    "
      >
        <button
          on:click={() => cancelDraftServiceAgreement($draftAgreement)}
          type="button"
          class="inline-flex w-auto justify-start rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-white sm:mr-3 sm:w-auto"
          >Cancel</button
        >
        <div class="flex gap-1">
          {#if !(JSON.stringify($draftAgreement) === JSON.stringify(storedDraftAgreement))}
            <button
              on:click={() => saveDraftServiceAgreement($draftAgreement)}
              type="button"
              class=" w-auto mr-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
              >Save</button
            >{/if}
          {#if canGenerate}
            <button
              on:click={() => generateServiceAgreement($draftAgreement)}
              type="button"
              title="Generate a pending service agreement from this draft"
              class=" w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
              >Generate</button
            >
          {/if}
        </div>
      </div>
    </li>
  </ul>
{/if}
