<script>
  import { slide } from "svelte/transition";
  import { jspa } from "@shared/jspa";
  import JSON2CSV from "@shared/JSON2CSV.svelte";
  import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";
  import { getDaysUntilDate, formatDate } from "@shared/utilities.js";
  import { debounce } from "lodash-es";
  import DocumentGrid from "./DocumentGrid.svelte";
  import { SpinnerStore } from "@app/Overlays/stores.js";
  import { onMount } from "svelte";
  import { BreadcrumbStore } from "@shared/stores.js";

  let clients = [];
  let participantList = [];
  let selectedParticipant = [];

  onMount(async () => {
    jspa("/Participant", "listClients", {}).then((result) => {
      clients = result.result;

      clients.forEach((client) => {
        if (client.archived != 1)
          participantList.push({
            option: client.client_name,
            value: client.client_id,
          });
      });

      participantList.sort((a, b) => a.option.localeCompare(b.option));

      participantList = participantList;  
    
    });

    BreadcrumbStore.set({
      path: [{ url: null, name: "Reports" }],
    });
  });

  

  let documents = [];
  let requestCounter = 0;

  function loadDocuments(selectedParticipant) {
    const currentRequest = ++requestCounter; // Increment counter on each call
    if (selectedParticipant.length === 0) {
      documents = []; // Immediately clear documents if no staff selected
      return;
    }

    jspa("/Document", "listDocumentsByParticipantIds", {
      participant_ids: selectedParticipant,
    }).then((result) => {
      if (currentRequest === requestCounter) {
        // Check if this is the latest request
        documents = result.result;

        // this will sort the documents by expiry date
        // documents = result.result.sort((a, b) => {
        //   if (!a.expiry_date) return 1; // Place documents without expiry date at the end
        //   if (!b.expiry_date) return -1; // Place documents without expiry date at the end
        //   const dateA = new Date(a.expiry_date);
        //   const dateB = new Date(b.expiry_date);
        //   return dateA - dateB;
        // });
      }
    });
  }

  function hasDatePassed(dateString) {
    const today = new Date();
    const expiryDate = new Date(dateString);
    return expiryDate < today;
  }

  function isDateWithinSixWeeks(dateString) {
    const SIX_WEEKS_IN_MS = 6 * 7 * 24 * 60 * 60 * 1000;
    const today = new Date();
    const sixWeeksLater = new Date(today.getTime() + SIX_WEEKS_IN_MS); // 6 weeks in milliseconds
    const targetDate = new Date(dateString);

    return targetDate < sixWeeksLater && targetDate >= today;
  }

  // Define the debounced function
  const debouncedLoadDocuments = debounce(loadDocuments, 50);

  // Reactive statement to call the debounced function
  $: selectedParticipant, debouncedLoadDocuments(selectedParticipant);

  const currentDate = new Date();
  const options = {
    day: "numeric",
    month: "long",
    year: "numeric",
    timeZone: "Australia/Sydney",
  };
  const formattedDate = currentDate.toLocaleDateString("en-AU", options);
  // console.log(formattedDate);

  // TODO: This is a duplicate of code found in Document.svelte.  This should be refactored into a shared utility function.
  function openFile(vultr_storage_ref) {
    SpinnerStore.set({ show: true, message: "Getting File" });

    jspa("/Storage", "getS3ObjectFile", { key: vultr_storage_ref })
      .then((result) => {
        //console.log(result)
        let fileContent = result.result;
        let fileName = vultr_storage_ref.substring(33);

        // Decode base64 content
        let decodedContent = atob(fileContent);
        let byteNumbers = new Array(decodedContent.length);
        for (let i = 0; i < decodedContent.length; i++) {
          byteNumbers[i] = decodedContent.charCodeAt(i);
        }
        let byteArray = new Uint8Array(byteNumbers);

        // Determine the MIME type based on the file extension or content
        let mimeType;
        if (fileName.endsWith(".pdf")) {
          mimeType = "application/pdf";
        } else if (fileName.endsWith(".txt")) {
          mimeType = "text/plain";
        } else if (fileName.endsWith(".jpg") || fileName.endsWith(".jpeg")) {
          mimeType = "image/jpeg";
        } else if (fileName.endsWith(".png")) {
          mimeType = "image/png";
        } else {
          mimeType = "application/octet-stream";
        }

        const blob = new Blob([byteArray], { type: mimeType });

        const url = URL.createObjectURL(blob);

        // Open the URL in a new tab
        window.open(url, "_blank");

        // Optionally revoke the URL after a short delay
        setTimeout(() => {
          URL.revokeObjectURL(url);
        }, 10000); // adjust the timeout as needed
      })
      .finally(() => {
        SpinnerStore.set({ show: false });
      });
  }
</script>

<div class="no-print">
  <div class="mb-8">
    <div class="mb-2">
      <div
        class="text-2xl sm:truncate tracking-tight font-fredoka-one-regular text-indigo-950"
      >
        Required Documents Report
      </div>
      <p class=" text-sm text-gray-700">
        Select the Participant you want to view required documents for.
      </p>
    </div>

    <CheckboxButtonGroup options={participantList} bind:values={selectedParticipant} />
  </div>
</div>
{#if documents.length > 0}
  <div class="mb-2">
    <div class="flex justify-between items-center">
      <div>
        <div
          class="text-2xl sm:truncate tracking-tight font-fredoka-one-regular text-indigo-950"
        >
          Participant Documents
        </div>
        <p class=" text-sm text-gray-700">
          Here are the required documents for the selected Participant as of {formattedDate}.
        </p>
      </div>

      <div class="no-print">
        <JSON2CSV
          filename="documents.csv"
          bind:json_data={documents}
          label="Export results to CSV"
        />
      </div>
    </div>
  </div>

  <DocumentGrid bind:jsonData={documents} />

  <!-- <div class="no-print"> -->
  <table class="min-w-full divide-y divide-gray-300">
    <thead class=" text-xs">
      <tr>
        <th class="p-2 text-left font-medium">Name</th>
        <th class="p-2 text-left font-medium">Document</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white">
      {#each documents as document, index (index)}
        <tr
          in:slide={{ duration: 200 }}
          out:slide={{ duration: 200 }}
          class={document.document_status === "Missing" ? "text-gray-400" : ""}
        >
          <td class="p-2 text-left font-medium">{document.participant_name}</td>
          <td class="p-2 text-left font-medium">
            <div class="flex justify-between items-center">
              <div>
                <div class="font-semibold">
                  {#if document.file_reference}
                    <button
                      on:click={() => {
                        openFile(document.file_reference);
                      }}
                    >
                      {document.document_name}
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
                    </button>
                  {:else}
                    {document.document_name}
                  {/if}
                </div>
                {#if document.document_details}
                  <div class="text-xs">
                    {document.document_details}
                  </div>
                {/if}

                {#if document.expiry_date && !isDateWithinSixWeeks(document.expiry_date) && !hasDatePassed(document.expiry_date)}
                  <div class="text-xs">
                    Expires {formatDate(document.expiry_date)}
                  </div>
                {/if}

                {#if document.issue_date && !document.expiry_date}
                  <div class="text-xs">
                    Issued {formatDate(document.issue_date)}
                  </div>
                {/if}
                {#if document.expiry_date && isDateWithinSixWeeks(document.expiry_date)}
                  <div class="text-xs">
                    Renew by {formatDate(document.expiry_date)} ({getDaysUntilDate(
                      document.expiry_date
                    )})
                  </div>
                {/if}

                {#if document.expiry_date && hasDatePassed(document.expiry_date)}
                  <div class="text-xs">
                    Expired {formatDate(document.expiry_date)}
                    ({getDaysUntilDate(document.expiry_date)})
                  </div>
                {/if}
              </div>

              {#if document.expiry_date && hasDatePassed(document.expiry_date)}
                <span
                  class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700"
                  >Expired</span
                >
              {:else if document.document_status === "Missing"}
                <span
                  class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600"
                  >Missing</span
                >
              {:else if document.expiry_date && isDateWithinSixWeeks(document.expiry_date)}
                <span
                  class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800"
                  >Renewal Due</span
                >
              {:else}
                <span
                  class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700"
                  >Current</span
                >
              {/if}
            </div>
          </td>
        </tr>
      {/each}
    </tbody>
  </table>
  <!-- </div> -->
{/if}

<style>
  .vertical {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
  }

  @media print {
    tr {
      -webkit-print-color-adjust: exact; /* For WebKit browsers */
      background-color: inherit !important; /* Use the row's background color */
    }
    .no-print {
      display: none !important;
    }
  }
</style>
