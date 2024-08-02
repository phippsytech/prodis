<script>
    import { push } from "svelte-spa-router";
    import Container from "@shared/Container.svelte";
    import Role from "@shared/Role.svelte";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { formatDate } from "@shared/utilities";
    import ScaledNumberDisplay from "@shared/PhippsyTech/svelte-ui/ScaledNumberDisplay.svelte";
    import LeaveTypes from "@app/routes/Profile/Leave/LeaveTypes.svelte";

    BreadcrumbStore.set({
        path: [
            { url: "/payroll", name: "Payroll" },

            { url: null, name: "Leave" },
        ],
    });

    let status = "pending";

    let leave = [
        {
            Name: "Annual Leave",
            LeaveTypeID: "d7e69589-d4c4-4178-abc0-39fc961e6694",
        },
        {
            Name: "Long Service Leave",
            LeaveTypeID: "7371762f-4700-4599-81a4-a12f7554332c",
        },
        {
            Name: "Personal/Carer's Leave",
            LeaveTypeID: "b2a23bc7-642e-4e83-8135-41b554e7f956",
        },
        {
            Name: "Compassionate Leave (paid)",
            LeaveTypeID: "6f7b4f53-9fc9-4be7-a883-36a8bfced83f",
        },
        {
            Name: "Community Service Leave",
            LeaveTypeID: "98b13673-f3c4-4f31-8943-559085b81152",
        },
        {
            Name: "Unpaid Leave",
            LeaveTypeID: "3db723b9-7ab1-4dcf-b1dd-c071016a20ba",
        },
        {
            Name: "Annual Leave (5 Weeks)",
            LeaveTypeID: "6d07ae1d-34fb-48e2-a47d-1cfbc2e49500",
        },
    ];

    function getLeaveTypeName(leave_type_id) {
        let leave_type = leave.find(
            (item) => item.LeaveTypeID == leave_type_id,
        );
        return leave_type.Name;
    }

    let thing = [{}, {}, {}];

    let leave_applications = [];

    let scheduled_leave_applications = [];

    let processed_leave_applications = [];

    listLeaveApplications();

    function listLeaveApplications() {
        leave_applications = [];
        scheduled_leave_applications = [];
        processed_leave_applications = [];
        jspa("/Payroll/Leave", "listLeaveApplications", {}).then((result) => {
            leave_applications = result.result;

            scheduled_leave_applications = leave_applications.filter(
                (item) =>
                    item.LeavePeriods[0].leave_period_status == "SCHEDULED",
            );

            processed_leave_applications = leave_applications.filter(
                (item) =>
                    item.LeavePeriods[0].leave_period_status == "PROCESSED",
            );

            // sort leave_applications by start_date
            leave_applications.sort((a, b) => {
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
</script>

<div class=" mb-4">
    <div
        class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
    >
        Processed Leave
    </div>
    <div class="">This page contains processed leave requests.</div>
</div>

<Container>
    <!-- {#if status=="completed"} -->
    {#each processed_leave_applications as item, index}
        <div
            class="flex justify-start items-center gap-x-6 py-3 {index <
            processed_leave_applications.length - 1
                ? 'border-b border-b-indigo-100'
                : ''} lg:px-8"
        >
            <div class="text-2xl text-slate-900 w-20 text-center">
                <ScaledNumberDisplay
                    value={item.LeavePeriods[0].number_of_units}
                /> <span class="text-sm font-light text-slate-400">hrs</span>
            </div>
            <div>
                <span class="font-medium"
                    >{item.staffer.first_name} {item.staffer.last_name}</span
                >
                :
                <span class="font-light"
                    >{getLeaveTypeName(item.leave_type_id)}</span
                >
                <div class="text-sm font-medium text-slate-900">
                    {formatDate(
                        item.start_date,
                    )}{#if item.start_date != item.end_date}
                        - {formatDate(item.end_date)}{/if}
                </div>
                <div class="text-sm text-slate-900">{item.title}</div>
            </div>
        </div>
    {/each}
    <!-- {/if} -->
</Container>
