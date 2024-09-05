<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";

    // import DocumentList from '@shared/Document/List.svelte';
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingStateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingStateSelect.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";
    import UserComponent from "@shared/UserComponent.svelte";
    import EmployeeSelector from "@shared/Xero/EmployeeSelector.svelte";
    import StaffRoleSelector from "@shared/StaffRoleSelector.svelte";

    import { UserMinusIcon } from "heroicons-svelte/24/outline";
    // import StafferClients from './StafferClients.svelte';
    import Role from "@shared/Role.svelte";
    import { jspa } from "@shared/jspa.js";
    import { getStaffer } from "@shared/api.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { StafferStore, BreadcrumbStore } from "@shared/stores.js";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";

    import PayGradeSelector from "../Payroll/Staff/SILPayGrade.svelte";
    import { convertMicrosoftDate } from "@shared/utilities.js";

    import StaffKPIReport from "../StaffKPIReport.svelte";

    // You need to define the component prop "params"
    export let params = {};

    let staff_id = params.staff_id;
    let staffer = {};
    let stored_staffer = {};
    let mounted = false;
    let show = false;

    $: {
        if (mounted) {
            show = !(
                JSON.stringify(staffer) === JSON.stringify(stored_staffer)
            );
        }
    }

    onMount(async () => {
        staffer = await jspa("/Staff", "getStaffer", { id: staff_id }); //getStaffer(staff_id)
        staffer = staffer.result;
        stored_staffer = Object.assign({}, staffer);
        if (staffer.groups == null) staffer.groups = [];

        if (staffer.xero_employee_ref) staffer.employment_type = "employee";
        mounted = true;

        BreadcrumbStore.set({
            path: [
                { url: "/staff", name: "Staff" },
                { url: null, name: staffer.name },
            ],
        });
    });

    function archiveStaffer() {
        jspa("/Staff", "archiveStaffer", { id: staff_id }).then((result) => {
            staffer = result.result;
            // Make a copy of the object
            stored_staffer = Object.assign({}, staffer);
        });
    }

    function restoreStaffer() {
        jspa("/Staff", "restoreStaffer", { id: staff_id }).then((result) => {
            staffer = result.result;
            // Make a copy of the object
            stored_staffer = Object.assign({}, staffer);
        });
    }

    function fetchXeroEmployee(xero_employee_ref) {
        jspa("/Xero", "getEmployee", { employee_id: xero_employee_ref }).then(
            (result) => {
                let employee = result.result;

                staffer.first_name = employee.FirstName;
                staffer.last_name = employee.LastName;
                staffer.date_of_birth = employee.DateOfBirth;

                staffer.start_date = convertMicrosoftDate(employee.StartDate);

                staffer.address_line_1 = employee.HomeAddress.AddressLine1;
                staffer.address_line_2 = employee.HomeAddress.AddressLine2;
                staffer.suburb = employee.HomeAddress.City;
                staffer.state = employee.HomeAddress.Region;
                staffer.postcode = employee.HomeAddress.PostalCode;

                if (employee.Mobile) {
                    staffer.phone_number = employee.Mobile;
                } else {
                    if (employee.Phone) staffer.phone_number = employee.Phone;
                }

                if (!staffer.email) staffer.email = employee.Email;

                // Make a copy of the object
                // stored_staffer = Object.assign({}, staffer);
            },
        );
    }

    function undo() {
        staffer = Object.assign({}, stored_staffer);
    }

    async function save() {
        try {
            jspa("/Staff", "updateStaffer", staffer).then((result) => {
                stored_staffer = Object.assign({}, staffer);
            });
        } catch (error) {
            console.error(error);
        }
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(staffer) === JSON.stringify(stored_staffer)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

<div class="py-2 px-4 mb-2 flex justify-between items-center">
    <div>
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            {staffer.name}
        </div>
        {#if staffer.name != staffer.first_name + " " + staffer.last_name}<div
                class="text-xs text-gray-800"
            >
                {staffer.first_name}
                {staffer.last_name}
            </div>{/if}
    </div>
</div>

{#if staffer.archived == 1}
    <div
        class="bg-red-100 rounded-lg py-5 px-6 mb-4 text-base text-red-700 mb-3 flex justify-between"
        role="alert"
    >
        {staffer.name} has been archived.
        <Role roles={["admin"]}>
            <button
                on:click={() => restoreStaffer()}
                type="button"
                class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                >Restore
            </button>
        </Role>
    </div>
{/if}

{#if staffer.user_id}
    <UserComponent bind:user_id={staffer.user_id} />

    <FloatingDate label="Date of Birth" bind:value={staffer.date_of_birth} />

    <Container>
        <FloatingInput
            bind:value={staffer.address_line_1}
            label="Address Line 1"
            placeholder="eg: Unit 1"
        />
        <FloatingInput
            bind:value={staffer.address_line_2}
            label="Address Line 2"
            placeholder="eg: 123 Test St"
        />
        <FloatingInput
            bind:value={staffer.suburb}
            label="Suburb"
            placeholder="eg: Adelaide"
        />
        <FloatingStateSelect bind:value={staffer.state} />
        <FloatingInput
            bind:value={staffer.postcode}
            label="Postcode"
            placeholder="eg: 3000"
        />
    </Container>
{/if}

<!-- {#if staffer.xero_employee_ref}
    <div
        class="rounded-md bg-indigo-50 py-2 px-4 mb-2 border border-indigo-200"
    >
        <div class="flex-1 md:flex md:justify-between items-center">
            <div class="flex text-indigo-900">
                You can fetch this employee's details from Xero.
            </div>
            <button
                on:click={() => fetchXeroEmployee(staffer.xero_employee_ref)}
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Fetch from Xero</button
            >
        </div>
    </div>
{/if} -->

<Container>
    <div class="text-xs opacity-50 mb-2">Employment Details</div>

    <FloatingSelect
        bind:value={staffer.status}
        label="Status"
        instruction="Select Status"
        options={[
            {
                option: "Onboarding",
                value: "onboarding",
            },
            {
                option: "Current",
                value: "current",
            },
            {
                option: "Archived",
                value: "archived",
            },
        ]}
    />

    <FloatingDate label="Start Date" bind:value={staffer.start_date} />

    <FloatingSelect
        bind:value={staffer.employment_type}
        label="Employment Type"
        instruction="Select Employment Type"
        options={[
            {
                option: "Employee",
                value: "employee",
            },
            {
                option: "Contractor",
                value: "contractor",
            },
        ]}
    />

    <div class="bg-white px-3 pt-2 pb-4 mb-2 border border-gray-300 rounded-md">
        <div class="text-xs opacity-50 mb-2">Staff Groups</div>
        <CheckboxButtonGroup
            options={[
                { value: "therapist", option: "Therapist" },
                { value: "sil", option: "SIL" },
                { value: "admin", option: "Admin" },
            ]}
            bind:values={staffer.groups}
        />
    </div>
    <StaffRoleSelector bind:staff_role={staffer.staff_role} />

    <Role roles={["admin"]}>
        <FloatingInput
            bind:value={staffer.billable_hours_kpi}
            label="Billable Hours KPI (hours)"
            placeholder="eg: 25"
            inputmode="tel"
            autocomplete="off"
        />

        {#if staffer.employment_type == "employee"}
            {#if staffer.groups.includes("sil")}
                <PayGradeSelector bind:value={staffer.paygrade_id} />
            {/if}
        {/if}
        <EmployeeSelector bind:xero_employee_id={staffer.xero_employee_ref} />
    </Role>
</Container>

<Role roles={["admin"]}>
    <div class="flex justify-between">
        <div>
            {#if staffer.archived != 1}
                <button
                    on:click={() => archiveStaffer()}
                    type="button"
                    class="inline-block px-6 py-2 border-2 border-gray-200 text-gray-300 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
                    ><UserMinusIcon class="w-6 h-6 inline pr-2" /> Archive
                </button>
            {/if}
        </div>
    </div>
</Role>
