<script>
    import { onMount } from "svelte";
    import { push } from "svelte-spa-router";

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

    // import PayGradeSelector from '../Payroll/PayGrades/PayGradeSelector.svelte';
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
    let userComponent;

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

    // handles the delete event called from the DcocumentList component
    function deleteDocument(event) {
        let document_id = event.detail.document_id;
        staffer.documents.forEach((document, index) => {
            if (document.id == document_id) {
                staffer.documents[index].status = "deleting";
            }
        });
        let document = {
            id: event.detail.document_id,
            staff_id: params.id,
        };
        jspa("/Staff/Document", "deleteStafferDocument", document).then(
            (result) => {
                staffer.documents = staffer.documents.filter(
                    (document) => document.id !== event.detail.document_id,
                );
            },
        );
    }

    // function setStaffer(){

    //             StafferStore.update((currentData)=>{
    //                 let newData = currentData;
    //                 newData ={
    //                     id: staffer.id,
    //                     name: staffer.name,
    //                     masquerading: true
    //                 }
    //                 return newData;
    //             });

    // }

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
        userComponent.undo(); // this undoes any changes in the user component
    }

    async function save() {
        try {
            const user_id = await userComponent.upsert();
            jspa("/Staff", "updateStaffer", staffer).then((result) => {
                // Make a copy of the object

                stored_staffer = Object.assign({}, staffer);

                // push("/staff")
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

    function handleLoaded(event) {
        staffer.user = event.detail;
        stored_staffer.user = Object.assign({}, event.detail);
    }

    function handleChanged(event) {
        staffer.user = Object.assign({}, event.detail);
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
        <!-- <div class="text-2xl font-medium">Michael Phipps</div> -->
    </div>

    <!-- <Role roles={["admin"]} >
        <div class="flex justify-end ">
            <button on:click={()=>setStaffer()}  type="button" class="inline-block px-6 py-2 shadow flexitems-center border border-gray-600 text-gray-600  rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 mb-4">
            <svg  class="h-5 w-5 inline text-gray-600 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512"><path d="M288 64C64 64 0 160 0 272S80 448 176 448h8.4c24.2 0 46.4-13.7 57.2-35.4l23.2-46.3c4.4-8.8 13.3-14.3 23.2-14.3s18.8 5.5 23.2 14.3l23.2 46.3c10.8 21.7 33 35.4 57.2 35.4H400c96 0 176-64 176-176s-64-208-288-208zM96 256a64 64 0 1 1 128 0A64 64 0 1 1 96 256zm320-64a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>
            <span class="pl-2">Masquerade as {staffer.name}</span></button>
        </div>
    </Role> -->
</div>

<Role roles={["super"]}>
    <StaffKPIReport {staff_id} />
</Role>

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
    <UserComponent
        bind:this={userComponent}
        bind:user_id={staffer.user_id}
        on:loaded={(event) => handleLoaded(event)}
        on:changed={(event) => handleChanged(event)}
    />
{/if}

{#if staffer.xero_employee_ref}
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
{/if}

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

<Role roles={["admin"]}>
    <FloatingInput
        bind:value={staffer.billable_hours_kpi}
        label="Billable Hours KPI (hours)"
        placeholder="eg: 25"
        inputmode="tel"
        autocomplete="off"
    />

    {#if staffer.employment_type == "employee"}
        <!-- <FloatingInput bind:value={staffer.level} label="Pay Grade (used for house earnings rates)" placeholder="eg: 1 - 4" inputmode="tel" autocomplete="off"/> -->
        {#if staffer.groups.includes("sil")}
            <PayGradeSelector bind:value={staffer.paygrade_id} />
        {/if}
    {/if}
    <EmployeeSelector bind:xero_employee_id={staffer.xero_employee_ref} />
</Role>

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
