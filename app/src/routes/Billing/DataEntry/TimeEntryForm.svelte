<script>
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
    import ClientPlanServicesService from "@app/routes/Clients/ServiceAgreements/Services/ServiceItem.svelte";
    import PlanManagerSelector from "@app/routes/Accounts/PlanManagers/PlanManagerSelector.svelte";
    import StaffSelector from "./StaffSelector.svelte";
    import ClientSelector from "./ClientSelector.svelte";
    import ServiceSelector from "./ServiceSelector.svelte";
    import ClaimSelector from "./ClaimSelector.svelte";
    import Role from "@shared/Role.svelte";
    import { jspa } from "@shared/jspa.js";
    // import { StafferStore } from '@shared/stores.js';

    export let timetracking = {};
    export let mode = "add";

    let readOnly = false;
    let plan_manager_id = null;
    let stored_service_id;
    let record_travelled_kilometers = false;

    function setStaffId(staff_id) {
        timetracking.staff_id = staff_id;
    }
</script>

{#if timetracking.invoice_batch}
    <div class="rounded-md bg-blue-50 p-4 mb-2">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-blue-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <div class="ml-3 flex-col flex-1 md:flex md:justify-between">
                <p class="text-sm text-gray-700">
                    <b>Session billed.</b><br />Certain data is read-only to
                    maintain accurate billing.
                </p>
                <p class="text-xs text-gray-400">
                    Please let admin know if read-only data needs to be
                    corrected.
                </p>
            </div>
        </div>
    </div>
{/if}

<StaffSelector bind:staff_id={timetracking.staff_id} {readOnly} />
<ClientSelector bind:client_id={timetracking.client_id} {readOnly} />
<ServiceSelector
    bind:service_id={timetracking.service_id}
    bind:client_id={timetracking.client_id}
    {readOnly}
/>

{#if timetracking.staff_id && timetracking.client_id && timetracking.service_id && timetracking.service_id != "Choose service"}
    <ClientPlanServicesService
        bind:client_id={timetracking.client_id}
        bind:service_id={timetracking.service_id}
    />

    {#if mode == "edit"}
        <Role roles={["admin"]} conditional={true}>
            <div slot="authorised">
                <PlanManagerSelector
                    bind:planmanager_id={timetracking.planmanager_id}
                    {readOnly}
                />
            </div>
            <div slot="declined">
                <PlanManagerSelector
                    bind:planmanager_id={timetracking.planmanager_id}
                    readOnly={false}
                />
            </div>
        </Role>
    {/if}

    <FloatingDateSelect
        bind:value={timetracking.session_date}
        label="Date"
        {readOnly}
    />
    <ClaimSelector
        bind:claim_type={timetracking.claim_type}
        bind:cancellation_reason={timetracking.cancellation_reason}
        {readOnly}
    />

    {#if timetracking.claim_type == "TRAN"}
        <FloatingInput
            bind:value={timetracking.session_duration}
            label="Travel Time (in mins)"
            placeholder="eg: 90"
            inputmode="numeric"
            inputmask="xxxx"
            {readOnly}
        />

        {#if record_travelled_kilometers}
            <FloatingInput
                bind:value={timetracking.kilometers_travelled}
                label="Travel Kms"
                placeholder="eg: 7.5 (optional)"
                {readOnly}
            />
        {/if}
    {:else}
        <FloatingInput
            bind:value={timetracking.session_duration}
            label="Duration (in mins)"
            placeholder="eg: 90"
            inputmode="numeric"
            inputmask="xxxx"
            {readOnly}
        />
    {/if}

    <FloatingTextArea
        bind:value={timetracking.notes}
        label="Notes"
        placeholder="eg: Attended doctor appointment."
        style="height:350px"
    />
{/if}
