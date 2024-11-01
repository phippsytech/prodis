<script>
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { timeAgoOLD } from "@shared/utilities.js";
  import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
  import {
    EnvelopeIcon,
    ArchiveBoxIcon,
    DocumentTextIcon,
    DocumentArrowUpIcon,
    TrashIcon,
    UserIcon,
  } from "heroicons-svelte/24/outline";
  import { toastError, toastSuccess } from "@shared/toastHelper.js";
  import { slide } from "svelte/transition";
  import { flip } from "svelte/animate";
  import { quintOut } from "svelte/easing";
  import { writable } from "svelte/store";

  import FileSignatureIcon from "@shared/PhippsyTech/svelte-ui/icons/FileSignature.svelte";
  import FileContractIcon from "@shared/PhippsyTech/svelte-ui/icons/FileContract.svelte";
  import Spinner from "@shared/PhippsyTech/svelte-ui/Spinner.svelte";
  import { CloudArrowUpIcon } from "heroicons-svelte/24/outline";
  import FileUploader from "@shared/FileUploader.svelte";
  import { push, replace } from "@app/../node_modules/svelte-spa-router";

  export let params;

  let signature_request_id = params.id;

  let signature_request_events = [];
  let signature_request = {
    document: {
      url: null,
    },
  };

  let import_data = {};
  let uploading = false;
  let base64_file = null;
  let error_message = null;

  jspa("/SignatureRequest", "listSignatureRequestEvents", {
    signature_request_id,
  }).then((result) => {
    signature_request_events = result.result;
  });

  jspa("/SignatureRequest", "getSignatureRequest", {
    signature_request_id,
  }).then((result) => {
    signature_request = result.result;
    console.log(signature_request);
  });

  BreadcrumbStore.set({
    path: [
      { name: "Service Agreements", url: "/serviceagreements" },
      { name: "Pending", url: "/serviceagreements/pending" },
    ],
  });

  function getUnsignedDocument(signature_request_id) {
    jspa("/SignatureRequest", "getSignatureRequest", {
      signature_request_id,
    }).then((result) => {
      // open the document in new tab
      window.open(result.result.document.url, "_blank");
    });
  }

  function emailSignatureRequest(signature_request_id) {
    jspa("/SignatureRequest", "emailSignatureRequest", {
      signature_request_id,
    }).then((result) => {
      toastSuccess("Email Sent");
      console.log(result);
    });
  }

  function archiveSignatureRequest(signature_request_id) {
    if (
      confirm(
        "Are you sure you want to delete this item?  The document and any signature records will be lost.  This is not reversable."
      )
    ) {
      // User clicked OK
      jspa("/SignatureRequest", "archiveSignatureRequest", {
        signature_request_id,
      }).then((result) => {
        toastSuccess("Signature Request Archived");
        replace("/signaturerequests");
      });
    } else {
      // User clicked Cancel
      console.log("Action canceled by user.");
    }
  }

  function upload() {
    alert("This function is not available yet.");

    // uploading = true;

    // jspa("/SignatureRequest", "uploadSignedDocument", import_data)
    //     .then((result) => {
    //         uploading = false;
    //         import_data = {};
    //     })
    //     .catch((error) => {
    //         error_message = error.error_message;
    //     })
    //     .finally(() => {
    //         uploading = false;
    //     });
  }

  let icons = [];

  if (signature_request.signed_document_url) {
    icons = [
      {
        url: "/staff",
        name: "Signed Document",
        icon: FileContractIcon,
        role: ["admin"],
      },
    ];
  } else {
    icons = [
      {
        click: () => getUnsignedDocument(signature_request_id),
        name: "View Unsigned Document",
        icon: DocumentTextIcon,
        role: ["admin"],
      },

      {
        click: () => archiveSignatureRequest(signature_request_id),
        name: "Delete",
        icon: TrashIcon,
        role: ["admin"],
      },
    ];
  }
</script>

<div
  class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular px-2 mt-2"
>
  {signature_request.title}
</div>

<div class="flex items-center px-2">
  <span class="mr-4 flex items-center"
    ><UserIcon class="w-4 h-4 inline mb-1" />
    {signature_request.representative_name}
  </span>

  {#if signature_request.email}<span>
      <EnvelopeIcon class="w-4 h-4 inline  mb-1" />{signature_request.email}
    </span>{/if}
</div>

<div class="my-2">
  <IconMenu {icons} userRoles={["admin"]} />
</div>

<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">Activity</h3>
<ul
  class="bg-white rounded-lg border border-indigo-100 w-full text-slate-900 mb-4"
>
  {#each signature_request_events as event, index (event.id)}
    <li
      animate:flip={{
        duration: 500,
        easing: quintOut,
      }}
      in:slide={{ duration: 250 }}
      out:slide|local={{ duration: 250 }}
      class="group flex justify-between items-center hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 {signature_request_events.length -
        1 ==
      index
        ? 'rounded-b-lg '
        : ''}border-b border-indigo-100 w-full
                
                     
                "
    >
      <div class="staff-member">
        <div class="text-sm font-bold">
          {event.event}
        </div>
        <div class="text-xs"></div>
      </div>
      <div class="flex">
        {timeAgoOLD(event.created)}
      </div>
    </li>
  {:else}
    <li class="px-6 py-2 w-full rounded-t-lg text-slate-400 cursor-default">
      <div class="text-sm text-center text-slate-500 p-4 pt-2">
        <div
          class="flex justify-center items-center h-8 w-8 text-slate-300 mx-auto m-2"
        >
          <svg
            data-slot="icon"
            fill="none"
            stroke-width="1.5"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"
            ></path>
          </svg>
        </div>
        <div>No signature request events found</div>
      </div>
    </li>
  {/each}
</ul>

<!-- <div class="rounded-md bg-slate-100 pt-3 p-2 mb-2">
    <h3 class="text-sm text-slate-500 font-bold mx-1">
        Upload Hard Copy of Signed Document
    </h3>
    <div class="text-xs px-1 text-slate-500 mb-2">
        If the document was signed physically rather than electronically, please
        scan and upload the signed document here.
    </div>

    {#if !uploading}
        {#if error_message && import_data.base64_file}
            {error_message}
        {/if}

        <FileUploader
            bind:value={import_data.base64_file}
            bind:name={import_data.file_name}
        />

        {#if import_data.base64_file}
            <div class="flex justify-between">
                <span></span>
                <button
                    on:click={() => upload()}
                    type="button"
                    class="inline-block px-6 py-4 bg-blue-600 text-white font-medium text-sm leading-snug uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                    ><CloudArrowUpIcon class="w-5 h-5 inline" /> Upload</button
                >
            </div>
        {/if}
    {/if}
</div> -->
