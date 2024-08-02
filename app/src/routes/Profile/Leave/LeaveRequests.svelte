<script>
    import { push } from "svelte-spa-router";
    import Container from "@shared/Container.svelte";
    import Role from "@shared/Role.svelte";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { formatDate } from "@shared/utilities.js";

    export let staff_id = null;

    let leave_requests = [];

    let status = "pending";
    let thing = [{}, {}, {}];

    $: {
        listLeaveRequests(staff_id);
    }

    // listLeaveRequests()

    export function listLeaveRequests(staff_id) {
        leave_requests = [];
        jspa("/Payroll/Leave/LeaveRequest", "listLeaveRequestsByStaffId", {
            staff_id: staff_id,
        }).then((result) => {
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
        });
    }

    function deleteLeaveRequest(leave_request_id) {
        jspa("/Payroll/Leave/LeaveRequest", "deleteLeaveRequest", {
            id: leave_request_id,
        })
            .then((result) => {
                toastSuccess("Leave request deleted");
                listLeaveRequests(staff_id);
            })
            .catch((error) => {
                toastError("Error deleting leave request");
            });
    }
</script>

<Container>
    <div class=" text-sm leading-6 text-slate-900 font-semibold">
        Leave Requests
    </div>

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
                    <div class="text-xs text-slate-500">
                        {#if leave_request.leave_type == "annual"}Annual Leave{/if}
                        {#if leave_request.leave_type == "personal"}Personal/Carer's
                            Leave{/if}
                        {#if leave_request.leave_type == "unpaid"}Unpaid Leave{/if}
                        {#if leave_request.is_unpaid == 1}<span class="italic"
                                >(Unpaid)</span
                            >{/if}
                    </div>
                    <div class="text-sm font-medium text-slate-900">
                        {formatDate(
                            leave_request.start_date,
                        )}{#if leave_request.date_type == "range"}&nbsp;- {formatDate(
                                leave_request.end_date,
                            )}{/if}
                    </div>
                    <div class="text-sm text-slate-900">
                        {leave_request.reason}
                    </div>
                    <!-- <div class="rounded-md py-1 px-2 text-xs font-medium ring-1 ring-inset text-red-700 bg-red-50 ring-red-600/10">Unpaid</div> -->
                </div>
            </div>

            <button
                class="inline-block hover:text-indigo-600"
                on:click={() => deleteLeaveRequest(leave_request.id)}
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="h-6 w-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    ></path>
                </svg>
            </button>
        </div>
    {:else}
        <div class="text-sm text-slate-500">No leave requests</div>
    {/each}
</Container>
