<script>
    import Container from "@shared/Container.svelte";
    import PPTStaffSelector from "./PPTStaffSelector.svelte";
    import LeaveBalance from "./LeaveBalance.svelte";
    import LeaveRequestForm from "./LeaveRequestForm.svelte";
    import LeaveRequests from "./LeaveRequests.svelte";
    import LeaveApplications from "./LeaveApplications.svelte";

    import { onMount } from "svelte";
    import {
        UserStore,
        BreadcrumbStore,
        StafferStore,
    } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { haveCommonElements } from "@shared/utilities.js";
    import { RolesStore } from "@shared/stores.js";

    $: rolesStore = $RolesStore;
    $: userStore = $UserStore;

    onMount(async () => {
        if (userStore.id) {
            BreadcrumbStore.set({
                path: [
                    { name: userStore.name, url: "/profile" },
                    { url: null, name: "Leave" },
                ],
            });
        }
    });

    let leave_requests;
    let ppt = false;

    let leave = {
        staff_id: parseInt($StafferStore.id),
    };

    let staffer = {};

    function getStafferEmploymentType(staff_id) {
        jspa("/Staff", "getStaffer", { id: leave.staff_id }).then((result) => {
            staffer = result.result;
            if (
                staffer.employment_basis == "PARTTIME" ||
                staffer.employment_basis == "FULLTIME" ||
                staffer.employment_basis == "SALARY"
            ) {
                ppt = true;
            } else {
                ppt = false;
            }
        });
    }

    $: getStafferEmploymentType(leave.staff_id);

    function addLeaveRequest(leave) {
        // validate

        if (leave.staff_id == null) {
            toastError("Please select a staff member");
            return;
        }

        if (leave.leave_type == null) {
            toastError("Please select the type of leave you are requesting");
            return;
        }

        if (leave.reason == null) {
            toastError("Please enter a reason for leave");
            return;
        }

        if (leave.date_type == null) {
            toastError("Please select Date Range, Whole Day or Part of Day");
            return;
        }

        if (leave.date_type == "part_day" && leave.start_date == null) {
            toastError("Please enter the date for part day leave");
            return;
        }

        if (leave.hours == null) {
            toastError("Please enter the number of hours leave");
            return;
        }

        if (leave.date_type == "day" && leave.start_date == null) {
            toastError("Please enter the date of leave");
            return;
        }

        if (
            leave.date_type == "range" &&
            (leave.start_date == null || leave.end_date == null)
        ) {
            toastError("Please enter both the start and end date of leave");
            return;
        }

        if (leave.date_type == "range" && leave.start_date > leave.end_date) {
            toastError(
                "Please enter an end date greater than the start date of leave",
            );
            return;
        }

        jspa("/Payroll/Leave/LeaveRequest", "addLeaveRequest", leave).then(
            (result) => {
                const leave_request = result.result;
                toastSuccess("Leave Request added");
                leave_requests.listLeaveRequests(leave.staff_id);
            },
        );
    }
</script>

<div class="flex justify-between items-center mb-4">
    <div
        class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
    >
        Leave
    </div>
</div>

{#if haveCommonElements(rolesStore, ["payroll", "super"])}
    <PPTStaffSelector bind:staff_id={leave.staff_id} />
{/if}

<!-- {#if leave.staff_id==null && !haveCommonElements(rolesStore, ["payroll","super"])} -->
{#if !ppt}
    <Container>
        <div class="text-sm text-center text-gray-500 p-4 pt-2">
            <div
                class="flex justify-center items-center h-8 w-8 text-gray-300 mx-auto m-2"
            >
                <svg
                    data-slot="icon"
                    fill="currentColor"
                    stroke="none"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M 11.529297 1.5253906 A 4.5 4.5 0 0 0 7.5 6 A 4.5 4.5 0 0 0 16.5 6 A 4.5 4.5 0 0 0 11.529297 1.5253906 z M 12 11.984375 A 8.25 8.25 0 0 0 3.7519531 20.105469 A 0.75 0.75 0 0 0 4.1875 20.800781 C 6.5665 21.892781 9.214 22.5 12 22.5 A 18.683 18.683 0 0 0 19.8125 20.800781 A 0.75 0.75 0 0 0 20.248047 20.105469 A 8.25 8.25 0 0 0 12 11.984375 z M 9.6894531 14.224609 A 0.75 0.75 0 0 1 10.279297 14.460938 L 12 16.181641 L 13.720703 14.460938 A 0.75 0.75 0 1 1 14.779297 15.521484 L 13.060547 17.242188 L 14.779297 18.960938 A 0.75 0.75 0 1 1 13.720703 20.021484 L 12 18.300781 L 10.279297 20.021484 A 0.75 0.75 0 1 1 9.2207031 18.960938 L 10.939453 17.242188 L 9.2207031 15.521484 A 0.75 0.75 0 0 1 9.6894531 14.224609 z "
                    ></path>
                </svg>
            </div>
            <div>Leave requests can only be made for Full/Part-time staff.</div>
        </div>
    </Container>
{:else}
    <LeaveBalance bind:staff_id={leave.staff_id} />

    <Container>
        <div class=" text-sm leading-6 text-slate-900 px-2 font-semibold mb-2">
            Leave Request
        </div>

        <LeaveRequestForm bind:leave />

        <div class="flex justify-end items-center mb-4">
            <button
                on:click={() => addLeaveRequest(leave)}
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Add Leave Request</button
            >
        </div>
    </Container>

    <LeaveRequests bind:this={leave_requests} bind:staff_id={leave.staff_id} />

    <LeaveApplications bind:staff_id={leave.staff_id} />
{/if}
