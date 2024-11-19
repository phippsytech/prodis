<script>
  import { push } from "svelte-spa-router";
  import Container from "@shared/Container.svelte";
  import Role from "@shared/Role.svelte";
  import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { formatDate } from "@shared/utilities.js";

  export let staff_id = null;

  BreadcrumbStore.set({
    path: [
      { url: "/payroll", name: "Payroll" },
      { url: null, name: "Leave" },
    ],
  });

  let leave_requests = [];

  let status = "pending";
  let thing = [{}, {}, {}];

  $: {
    // listLeaveRequests()
  }

  listLeaveRequests();

  export function listLeaveRequests() {
    leave_requests = [];
    jspa("/Payroll/Leave/LeaveRequest", "listLeaveRequests", {}).then(
      (result) => {
        leave_requests = result.result;

        // sort leave_requests by start_date
        leave_requests.sort((a, b) => {
          if (a.start_date < b.start_date) {
            return -1;
          }
          if (a.start_date > b.start_date) {
            return 1;
          }
          return 0;
        });
      }
    );
  }

  function deleteLeaveRequest(leave_request_id) {
    if (confirm("Are you sure you want to delete this leave request?")) {
      jspa("/Payroll/Leave/LeaveRequest", "deleteLeaveRequest", {
        id: leave_request_id,
      })
        .then((result) => {
          toastSuccess("Leave request deleted");
          listLeaveRequests();
        })
        .catch((error) => {
          toastError("Error deleting leave request");
        });
    }
  }
</script>

<div class=" mb-4">
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Leave Requests
  </div>
  <div class="">
    This page contains staff leave requests that need to be approved or
    rejected.
  </div>
</div>

<Container>
  {#each leave_requests as leave_request, index}
    <div
      class="flex justify-between items-center gap-x-6 py-3 hover:bg-indigo-50 {index <
      leave_requests.length - 1
        ? 'border-b border-b-indigo-100'
        : ''} lg:px-8"
    >
      <div class="flex justify-start items-center gap-x-6">
        <div class="text-2xl text-slate-900 w-20 text-center">
          {leave_request.hours}
          <span class="text-sm font-light text-slate-400">hrs</span>
        </div>
        <div>
          <div class="text-sm">
            <span class="font-medium">{leave_request.staff_name}</span>
            :
            <span class="font-light">
              {#if leave_request.leave_type == "annual"}Annual Leave{/if}
              {#if leave_request.leave_type == "personal"}Personal/Carer's Leave{/if}
              {#if leave_request.is_unpaid == 1}<span class="italic"
                  >(Unpaid)</span
                >{/if}
            </span>
          </div>
          <div class="text-sm font-medium text-slate-900">
            {formatDate(leave_request.start_date)}
            {#if leave_request.date_type == "range"}
              - {formatDate(leave_request.end_date)}{/if}
          </div>
          <div class="text-sm text-slate-900">
            {leave_request.reason}
          </div>
          <!-- <div class="rounded-md py-1 px-2 text-xs font-medium ring-1 ring-inset text-red-700 bg-red-50 ring-red-600/10">Unpaid</div> -->
        </div>
      </div>

      <div class="flex gap-1">
        <button
          on:click={() => deleteLeaveRequest(leave_request.id)}
          type="button"
          class="inline-flex w-auto justify-start rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-white sm:mr-3 sm:w-auto"
          >Delete</button
        >
      </div>

      <!-- 
            <button class="inline-block hover:text-indigo-600" on:click={()=>deleteLeaveRequest(leave_request.id)}>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button> -->
    </div>
  {:else}
    <div class="text-sm text-slate-500">No leave requests</div>
  {/each}
</Container>
