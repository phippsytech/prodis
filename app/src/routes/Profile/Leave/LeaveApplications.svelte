<script>
    import { push } from "svelte-spa-router";
    import Container from "@shared/Container.svelte";
    import Role from "@shared/Role.svelte";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";
    import { formatDate } from "@shared/utilities";
    import ScaledNumberDisplay from "@shared/PhippsyTech/svelte-ui/ScaledNumberDisplay.svelte";

    export let staff_id = null;

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

    $: {
        listLeaveApplications(staff_id);
    }

    // listLeaveApplications()

    export function listLeaveApplications(staff_id) {
        leave_applications = [];
        scheduled_leave_applications = [];
        processed_leave_applications = [];
        jspa("/Payroll/Leave", "listLeaveApplicationsByStaffId", {
            staff_id: staff_id,
        }).then((result) => {
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

    function deleteLeaveApplication(leave_application_id) {
        jspa("/Leave", "deleteLeaveApplication", {
            id: leave_application_id,
        }).then((result) => {
            if (result.status == "success") {
                toastSuccess("Leave application deleted");
                // loadLeaveApplications()
            } else {
                toastError("Error deleting leave application");
            }
        });
    }
</script>

<Container>
    <div class=" text-sm leading-6 text-gray-900 px-2 font-semibold">
        Processed Leave
    </div>

    <!-- <div>
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-2" aria-label="Tabs">
                
                <span on:click={()=>status="pending"} class="{(status=="pending")?'border-indigo-500 text-indigo-600':'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 cursor-pointer'} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium" >Not yet paid</span>

                <span on:click={()=>status="completed"} class="{(status=="completed")?'border-indigo-500 text-indigo-600':'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 cursor-pointer'} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">Completed</span>
                
            </nav>
        </div>
    </div>
     -->

    <!-- {#if status=="pending"}
        {#each scheduled_leave_applications as item, index}
            <div class="flex justify-between items-center gap-x-6 py-3 hover:bg-indigo-50 {(index <scheduled_leave_applications.length-1)?'border-b border-b-gray-900/10':''} lg:px-8 ">
                <div class="flex justify-start items-center gap-x-6">
                    <div class="">
                        <ScaledNumberDisplay value={item.LeavePeriods[0].number_of_units} /> <span class="text-sm font-light text-gray-400">hrs</span>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">{formatDate(item.start_date)}{#if item.start_date!=item.end_date} - {formatDate(item.end_date)}{/if}</div>
                        <div class="text-xs text-gray-500">{getLeaveTypeName(item.leave_type_id)}</div> 

                        <div class="text-sm text-gray-900">{item.title}</div>

                        
                    </div>
                </div>
            </div>
        {/each}
    {/if} -->

    <!-- {#if status=="completed"} -->
    {#each processed_leave_applications as item, index}
        <div
            class="flex justify-start items-center gap-x-6 py-3 {index <
            processed_leave_applications.length - 1
                ? 'border-b border-b-gray-900/10'
                : ''} lg:px-8"
        >
            <div class="">
                <ScaledNumberDisplay
                    value={item.LeavePeriods[0].number_of_units}
                /> <span class="text-sm font-light text-gray-400">hrs</span>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-900">
                    {formatDate(
                        item.start_date,
                    )}{#if item.start_date != item.end_date}
                        - {formatDate(item.end_date)}{/if}
                </div>
                <div class="text-xs text-gray-500">
                    {getLeaveTypeName(item.leave_type_id)}
                </div>
                <div class="text-sm text-gray-900">{item.title}</div>
            </div>
        </div>
    {/each}
    <!-- {/if} -->
</Container>
