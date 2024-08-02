<script>
    import { push } from "svelte-spa-router";
    import Container from "@shared/Container.svelte";
    import StaffSelector from "@app/routes/Billables/StaffSelector.svelte";
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
    import LeaveTypes from "@app/routes/Profile/Leave/LeaveTypes.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import Role from "@shared/Role.svelte";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";
    import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";

    let thing = [{}, {}, {}];

    let leave = {};

    let leave_types = [
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
</script>

<div class=" mb-4">
    <div
        class="text-3xl tracking-tight font-fredoka-one-regular"
        style="color:#220055;"
    >
        Apply for Leave
    </div>
    <!-- Only Full / Part-time staff are eligible to apply for leave.
    I potentially need to store an hours per day figure. -->
</div>

<Container>
    <Role roles={["super"]}>
        <StaffSelector bind:staff_id={leave.staff_id} />
    </Role>

    <!-- Annual Leave Balance: 12.2342 Hours -->

    <div class="p-2 mb-2">
        <RadioButtonGroup
            columns={2}
            options={[
                { option: "Annual Leave", value: "annual" },
                { option: "Personal/Carer's Leave", value: "personal" },
            ]}
            bind:value={leave.leave_type}
        />
    </div>

    <div class="mb-4 px-2">
        <Toggle
            bind:value={leave.is_unpaid}
            label_on="Unpaid Leave"
            label_off="Unpaid Leave"
        />
    </div>

    <FloatingInput
        label="Reason for leave"
        bind:value={leave.reason}
        placeholder="eg: vacation"
    />

    <div class="p-2 mb-2">
        <RadioButtonGroup
            options={[
                { value: "range", option: "Date Range" },
                { value: "day", option: "Whole Day" },
                { value: "part_day", option: "Part of Day" },
            ]}
            bind:value={leave.date_type}
        />
    </div>

    <!-- <div class="text-sm mb-1 font-bold opacity-50">Period</div> -->
    <div class="flex flex-row justify-between gap-2 items-center">
        <div class="flex-grow">
            <div
                class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
            >
                <label class="text-xs opacity-50 p-0 m-0 block"
                    >Start Date</label
                >
                <input
                    type="date"
                    class="w-full p-0 m-0 outline-none bg-white"
                    bind:value={leave.start_date}
                />
            </div>
        </div>

        {#if leave.date_type == "range"}
            <div class="flex-grow">
                <div
                    class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
                >
                    <label class="text-xs opacity-50 p-0 m-0 block"
                        >End Date</label
                    >
                    <input
                        type="date"
                        class="w-full p-0 m-0 outline-none bg-white"
                        bind:value={leave.end_date}
                    />
                </div>
            </div>
        {/if}
    </div>

    <!-- // Leave Period
    
   // pay period start date
   // pay period end date
   NumberofUnits -->

    {#if leave.date_type == "part_day"}
        <FloatingInput
            label="Hours"
            type="number"
            bind:value={leave.hours}
            placeholder="eg: 7.6"
        />
    {/if}
</Container>

<Container>
    <div class=" text-sm leading-6 text-gray-900 px-2 font-semibold">
        Leave requests
    </div>

    <p class=" px-2 text-xs text-gray-700">
        Note: For paid leave, if there is insufficient leave balance at the time
        of processing, the remaining portion of your pending leave will
        automatically be converted to unpaid leave.
    </p>

    {#each thing as item, index}
        <div
            class="flex justify-start items-center gap-x-6 py-3 {index <
            thing.length - 1
                ? 'border-b border-b-gray-900/10'
                : ''} lg:px-8"
        >
            <div class="text-2xl text-gray-900">
                7.6 <span class="text-sm font-light text-gray-400">hrs</span>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-900">
                    13 Nov 2023 - 21 Nov 2023
                </div>
                <div class="text-xs text-gray-500">Personal/Carer's Leave</div>
                <!-- <div class="rounded-md py-1 px-2 text-xs font-medium ring-1 ring-inset text-red-700 bg-red-50 ring-red-600/10">Unpaid</div> -->
            </div>
        </div>
    {/each}
</Container>
