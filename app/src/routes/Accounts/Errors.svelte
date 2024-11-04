<script>
  import { flip } from "svelte/animate";
  import { quintOut } from "svelte/easing";
  import { fade } from "svelte/transition";
  import { fly } from "svelte/transition";
  import { slide } from "svelte/transition";
  import { jspa } from "@shared/jspa.js";
  import { push } from "svelte-spa-router";
  import { formatDate, formatCurrency } from "@shared/utilities";
  import { XMarkIcon, PencilIcon } from "heroicons-svelte/24/outline";
  // import ErrorOptions from "./Errors/Options.svelte";

  let line_items = [];

  jspa("/Invoice/NDIA/PaymentRequestStatus", "getErrors", {}).then((result) => {
    line_items = result.result;
  });

  let claim_types = [
    { option: "Direct Service", value: "" },
    { option: "Cancellation", value: "CANC" },
    { option: "NDIA Required Report", value: "REPW" },
    { option: "Provider Travel", value: "TRAN" },
    {
      option: "Non-Face-to-Face Services",
      value: "NF2F",
    },
  ];

  function getOption(value) {
    if (value === null) value = "";
    let option = "";
    claim_types.forEach((item) => {
      if (item.value === value) {
        option = item.option;
      }
    });
    return option;
  }

  function formatErrorMessage(error_message, index) {
    // return error_message;
    // the error_message is a string with semicolons.  Split it into an array and return the first element
    return error_message.split(";")[index];
    // return error_message.replace(/(?:\r\n|\r|\n)/g, "<br>");
  }

  function resubmit(payment_request_id) {
    jspa("/Invoice/NDIA/PaymentRequestStatus", "resubmit", {
      payment_request_id: payment_request_id,
    })
      .then((result) => {
        // I want to remove the line item from the list of errors
        line_items = line_items.filter(
          (item) => item.payment_request_id !== payment_request_id
        );
      })
      .catch((error) => {});
  }
</script>

<div class="px-2 mb-2">
  <h2 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Errors
  </h2>
  <p>
    These billing items have been rejected by the NDIS portal. Correct the
    errors and try again.
  </p>
</div>

<div class="hidden xs:block">
  <div
    class="grid grid-cols-2 items-center py-1 text-xs font-medium text-gray-500"
    style="grid-template-columns: 6fr auto;"
  >
    <div
      class="grid grid-cols-3 gap-4 items-center"
      style="grid-template-columns: auto 2fr 2fr;"
    >
      <div class="flex items-center">Date</div>
      <div>Service <span class="text-xs">(Claim Type)</span></div>
      <div>Quantity <XMarkIcon class="inline h-3 w-3" /> Unit Price</div>
    </div>
    <div class="text-right">Total</div>
  </div>
</div>

{#each line_items as line_item, index (line_item.id)}
  <div
    animate:flip={{ delay: 0, duration: 250, easing: quintOut }}
    out:fade={{ duration: 250, easing: quintOut }}
    in:slide={{
      axis: "y",
      duration: 150,
      delay: index * 10,
    }}
    class=" border-t border-indigo-100 pt-2 mt-2"
  >
    <div class="flex justify-between items-center gap-x-2">
      <div>
        <div class="font-semibold">
          {formatErrorMessage(line_item.error_message, 1)}
        </div>
        <div class="text-xs text-gray-500">
          {formatErrorMessage(line_item.error_message, 0)}
        </div>
      </div>

      <button
        on:click={() => resubmit(line_item.id)}
        type="button"
        class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
        >Resubmit</button
      >
    </div>
    <div
      class="grid grid-cols-2 gap-4 items-center hover:text-indigo-600 hover:bg-indigo-50 text-sm"
      style="grid-template-columns: 6fr auto;"
    >
      <div
        class="grid grid-cols-1 gap-0 xs:gap-4 xs:grid-cols-3 w-full items-center p-0"
        style="grid-template-columns: auto 2fr 2fr;"
      >
        <div class="flex items-center">
          <span class="hidden sm:inline"
            >{formatDate(line_item.supports_delivered_from, {
              day: "numeric",
              month: "short",
              year: "numeric",
            })}</span
          >
          <span class="inline sm:hidden"
            >{formatDate(line_item.supports_delivered_from, {
              day: "numeric",
              month: "short",
              year: null,
            })}</span
          >
        </div>
        <div class="">
          <!-- {item.SessionId} -->

          {line_item.support_number}
          <span class="text-xs hidden sm:inline"
            >({getOption(line_item.claim_type)})</span
          >
        </div>
        <div class="flex items-center">
          {line_item.quantity}
          <XMarkIcon class="inline h-3 w-3" />
          {formatCurrency(parseFloat(line_item.unit_price))}
        </div>
      </div>
      <div class="text-right items-center">
        {formatCurrency(line_item.paid_total_amount)}
      </div>
    </div>

    {#each line_item.billables as billable, index}
      <div
        class="grid grid-cols-2 gap-4 items-center hover:text-indigo-600 hover:bg-indigo-50 text-gray-500 text-sm"
        style="grid-template-columns: 6fr auto;"
      >
        <div
          class="grid grid-cols-1 gap-0 xs:gap-4 xs:grid-cols-3 w-full items-center p-0"
          style="grid-template-columns: auto 2fr 2fr;"
        >
          <div class="flex items-center">
            <span class="hidden sm:inline"
              >{formatDate(billable.SupportsDeliveredFrom, {
                day: "numeric",
                month: "short",
                year: "numeric",
              })}</span
            >
            <span class="inline sm:hidden"
              >{formatDate(billable.SupportsDeliveredFrom, {
                day: "numeric",
                month: "short",
                year: null,
              })}</span
            >
          </div>
          <div class="">
            <!-- {item.SessionId} -->
            {billable.ClientName} :
            {billable.ServiceCode}
            <span class="text-xs hidden sm:inline"
              >({getOption(billable.ClaimType)})</span
            >
          </div>
          <div class="flex items-center">
            {billable.Quantity}
            <XMarkIcon class="inline h-3 w-3" />
            {formatCurrency(parseFloat(billable.UnitPrice))}
          </div>
        </div>
        <div class="text-right items-center">
          {formatCurrency(billable.Quantity * billable.UnitPrice)}

          {#if billable.ClaimType == "TRAN"}
            <button
              on:click={() => push("/trips/" + billable.TripId)}
              type="button"
              class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
              >Edit</button
            >
          {:else}
            <button
              on:click={() => push("/billables/" + billable.SessionId)}
              type="button"
              class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
              >Edit</button
            >
          {/if}

          <!-- <button
                        on:click={() =>
                            push("/billables/" + billable.SessionId)}
                        type="button"
                        class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                        >Edit</button
                    > -->
        </div>
      </div>
    {/each}
  </div>
{:else}
  <div
    transition:fade={{ delay: 250, duration: 500, easing: quintOut }}
    class=" border-t border-gray-200 pt-2 mt-2"
  >
    Yay! No errors
  </div>
{/each}
