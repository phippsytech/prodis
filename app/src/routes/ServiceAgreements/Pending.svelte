<script>
  import FileContractIcon from "@shared/PhippsyTech/svelte-ui/icons/FileContract.svelte";

  import {
    EnvelopeIcon,
    ArchiveBoxIcon,
    TrashIcon,
    DocumentTextIcon,
    UserIcon,
  } from "heroicons-svelte/24/outline";
  import { toastError, toastSuccess } from "@shared/toastHelper.js";
  import { slide } from "svelte/transition";
  import { flip } from "svelte/animate";
  import { quintOut } from "svelte/easing";
  import { writable } from "svelte/store";

  import { push } from "svelte-spa-router";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { formatDate, timeAgo, timeAgoOLD } from "@shared/utilities.js";

  let request = [];

//   let service_agreements = [];

//   jspa("/ServiceAgreement", "listServiceAgreementsByStatus", {
//     status: "pending",
//   }).then((result) => {
//     service_agreements = result.result;
//     console.log(service_agreements);
//   });

  document.title = "Pending Service Agreements";
  BreadcrumbStore.set({
    path: [{ name: "Service Agreements", url: "/#/serviceagreements" }],
  });

  let signature_requests = writable([]);

  jspa("/SignatureRequest", "listSignatureRequests", {}).then((result) => {
    signature_requests.set(result.result);
  });

  BreadcrumbStore.set({ path: [{ name: "Signature Requests" }] });

  function getSignatureRequest(signature_request) {
    const signature_request_id = signature_request.id;
    if (signature_request.signed_document_url) {
      window.open(signature_request.signed_document_url, "_blank");
    } else {
      jspa("/SignatureRequest", "getSignatureRequest", {
        signature_request_id,
      }).then((result) => {
        // open the document in new tab
        window.open(result.result.document.url, "_blank");
      });
    }
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
        signature_requests.update((staff) => {
          const updatedSignatureRequest = staff.filter(
            (request) => request.id !== signature_request_id
          );
          return updatedSignatureRequest;
        });

        toastSuccess("Signature Request Archived");
      });
    } else {
      // User clicked Cancel
      console.log("Action canceled by user.");
    }
  }
</script>

<!-- 
<p>Pending statuses:</p>
<ul>
    <li>Preparing</li>
    <li>Sent</li>
    <li>Viewed</li>
    <li>In progress</li>
    <li>Signed</li>
    <li>Declined</li>
</ul> -->

<h1 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Pending Service Agreements
</h1>

<ul
  class="bg-white rounded-lg border border-indigo-100 w-full text-slate-900 my-4"
>
  {#each $signature_requests as request, index (request.id)}
    <li
      animate:flip={{
        duration: 500,
        easing: quintOut,
      }}
      in:slide={{ duration: 250 }}
      out:slide|local={{ duration: 250 }}
      class="group flex justify-between items-center hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 {$signature_requests.length -
        1 ==
      index
        ? 'rounded-b-lg '
        : ''}border-b border-indigo-100 w-full
              
                   {!request.signed_document_url
        ? ' '
        : ' border-l-indigo-600 border-l-4 '}
              
              "
    >
      <div class="staff-member">
        <div class="text-sm font-bold">
          <a
            class="underline text-indigo-600"
            href="/#/signaturerequests/{request.id}">{request.title}</a
          >
        </div>
        <div class="text-xs">
          #{request.id}
          {#if request.signed_document_url}Signed{:else}{request.most_recent_event}

            ({timeAgoOLD(request.event_created)}).
            <UserIcon
              class="w-3 h-3 inline mb-1"
            />{request.representative_name}
            {#if request.email}({request.email}){/if}
          {/if}
        </div>
      </div>
      <div class="flex">
        <button
          on:click={() => getSignatureRequest(request)}
          class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
          title="View Document"><DocumentTextIcon class="h-4 w-4" /></button
        >

        {#if request.email}
          <button
            on:click={() => emailSignatureRequest(request.id)}
            class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
            title="Email Document"><EnvelopeIcon class="h-4 w-4" /></button
          >{:else}
          <span
            class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-slate-200 transition duration-300"
            title="Can't Email"><EnvelopeIcon class="h-4 w-4" /></span
          >
        {/if}
        <button
          on:click={() => archiveSignatureRequest(request.id)}
          type="button"
          class="flex p-1 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
          title="Delete Document"
          ><TrashIcon class="h-4 w-4 inline m-0 p-0" /></button
        >
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
        <div>No signature requests found</div>
      </div>
    </li>
  {/each}
</ul>
