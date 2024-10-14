<script>
  import { onMount } from "svelte";
  import { ModalStore } from "@app/Overlays/stores.js";
  import Document from "./Document.svelte";
  import { jspa } from "@shared/jspa.js";
  import { formatDate } from "@shared/utilities.js";
  import { SpinnerStore } from "@app/Overlays/stores.js";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";

  $: modal = $ModalStore;

  export let document = {};
  export let participant_id;
  export let required = false;

  let participantDocument = {};
  
  let loaded = false;

  onMount(async () => {
    jspa("/Participant/Document", "getDocument", {
      participant_id: participant_id,
      document_id: document.id,
    })
      .then((result) => {
        participantDocument = result.result;
        // if(result.result.length > 0) participantDocument = result.result;//[0];
      })
      .finally(() => {
        loaded = true;
      });
  });

  function updateDocument() {
    // make a copy of the props so if the data is cleared we can still use it
    let data = Object.assign({}, modal.props);

    data.document_id = document.id;
    data.participant_id = participant_id;

    if (!data.id) {

      console.log('document', document);
      
      SpinnerStore.set({ show: true, message: "Adding Document" });
      jspa("/Participant/Document", "addParticipantDocument", data)
        .then((result) => {
          participantDocument = result.result;
        })
        .finally(() => {
          SpinnerStore.set({ show: false });
        });
    } else {
      SpinnerStore.set({ show: true, message: "Updating Document" });
      jspa("/Participant/Document", "updateParticipantDocument", data)
        .then((result) => {
          participantDocument = result.result;
        })
        .finally(() => {
          SpinnerStore.set({ show: false });
        });
    }
  }

  function deleteDocument() {
    SpinnerStore.set({ show: true, message: "Deleting Document" });
    jspa("/Participant/Document", "deleteParticipantDocument", {
      id: participantDocument.id,
    })
      .then((result) => {
        participantDocument = {};
      })
      .catch((error) => {
        toastError(error.message);
      })
      .finally(() => {
        SpinnerStore.set({ show: false });
      });
  }

  function editDocument() {
    (participantDocument.date_collection_option = document.date_collection_option),
      ModalStore.set({
        label: document.name,
        description: document.description,
        show: true,
        props: participantDocument,
        component: Document,
        action_label: "Update",
        action: () => updateDocument(),
        delete: () => deleteDocument(),
      });
  }

  $: valid = () => isValid();

  function isValid() {
    if (participantDocument) {
      if (Object.keys(participantDocument).length == 0) {
        return false;
      }

      if (participantDocument.id == null) {
        return false;
      }

      if (participantDocument.document_date == null) return false;

      let document_expires = new Date(participantDocument.document_date);

      if (document.date_collection_option == "issued") {
        const expiry_year =
          document_expires.getFullYear() +
          parseInt(document.years_until_expiry);
        document_expires.setFullYear(expiry_year);

        if (document_expires < new Date()) return false;
      }

      if (document.date_collection_option == "expires") {
        if (!participantDocument.document_date) return false;
        if (document_expires < new Date()) return false;
      }

      return true;
    } else {
      return false;
    }
  }
</script>

{#if loaded}
  <div
    on:click={() => editDocument()}
    class="relative flex items-center space-x-3 rounded-lg border {valid()
      ? 'border-green-300 bg-green-50'
      : required || participantDocument.document_date
        ? 'border-red-300 bg-red-50'
        : 'border-gray-300 bg-white'} px-4 py-3 shadow-sm hover:bg-indigo-600 hover:text-white group"
  >
    <div class="flex-shrink-0">
      {#if valid()}
        <svg
          class="h-6 w-6 text-green-600"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
          aria-hidden="true"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          ></path>
        </svg>
      {:else if required || participantDocument.document_date}
        <svg
          class="h-6 w-6 text-red-600"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
          aria-hidden="true"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
          ></path>
        </svg>
      {:else}
        <svg
          class="h-6 w-6 text-gray-500"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
          aria-hidden="true"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M12 6v12m6-6H6"
          ></path>
        </svg>
      {/if}
    </div>

    <div class="min-w-0 flex-1">
      <span class="focus:outline-none">
        <span class="absolute inset-0" aria-hidden="true"></span>
        <p
          class="text-sm font-medium group-hover:text-white flex justify-between"
        >
          {document.name}

          {#if participantDocument.vultr_storage_ref}
            <svg
              class="h-4 w-4 inline"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"
              ></path>
            </svg>
          {/if}
        </p>

        {#if document.description}
          <p class="text-xs text-gray-600">
            {document.description}
          </p>
        {/if}

        {#if participantDocument && Object.keys(participantDocument).length > 0}
          {#if participantDocument.details}
            <div class="text-sm">
              {participantDocument.details}
            </div>{/if}

          {#if document.date_collection_option == "expires" && participantDocument.document_date}
            <div class="text-xs font-light">
              Expires: {formatDate(participantDocument.document_date)}
            </div>
          {/if}

          {#if document.date_collection_option == "issued" && participantDocument.document_date}
            <div class="text-xs font-light">
              Issued: {formatDate(participantDocument.document_date)}
            </div>
          {/if}
        {:else}{/if}
      </span>
    </div>
  </div>
{:else}
  <div
    class="relative flex justify-center items-center space-x-3 rounded-lg bg-white px-4 py-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:bg-indigo-600 hover:text-white group"
  >
    <svg
      class="animate-spin flex-shrink-0 h-5 w-5 text-indigo-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
  </div>
{/if}

<!-- CE: {document_expires} -->
