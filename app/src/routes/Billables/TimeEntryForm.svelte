<script>
    import Container from "@shared/Container.svelte";
    import FloatingDurationSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDurationSelect.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import ServiceBooking from "@app/routes/Clients/ServiceAgreements/ServiceBookings/ServiceBooking.svelte";
    import PlanManagerSelector from "@app/routes/Accounts/PlanManagers/PlanManagerSelector.svelte";
    import StaffSelector from "./StaffSelector.svelte";
    import ClientSelector from "./ClientSelector.svelte";
    import ServiceButtonGroup from "./ServiceButtonGroup.svelte";
    import ClaimSelector from "./ClaimSelector.svelte";
    import Role from "@shared/Role.svelte";
    import RTE from "@shared/RTE/RTE.svelte";
    import { onMount } from "svelte";
    import { convertMinutesToHoursAndMinutes, getDateOnlyTimestamp } from "@shared/utilities";
    import { jspa } from "@shared/jspa.js";

    export let timetracking = {};
    export let budget_exceeded = false;
    export let available_session_duration = null;
    export let mode = "add";

    let service_agreement = {};
    let readOnly = false;
    let plan_manager_id = null;
    let stored_service_id;
    let hasActiveServiceAgreement = true;
    let hasDuplicate = false;

    // are these two the same thing?
    let serviceBooking = {};

    let record_travelled_kilometers = false;
    let client_on_hold = false;

    if (timetracking.invoice_batch) {
        readOnly = true;
    }

    onMount(() => {
        getAvailableSessionDuration();
        stored_service_id = timetracking.service_booking_id;
        
        if (timetracking.client_id) {
            loadServices(timetracking.client_id);
        }
    });

    $: console.log("Time Tracking: " + JSON.stringify(timetracking));

    $: if (timetracking.service_booking_id != stored_service_id) {
        getAvailableSessionDuration();
        stored_service_id = timetracking.service_booking_id;
    }

    $: if (
        (parseInt(timetracking.session_duration) || 0) >
        available_session_duration
    ) {
        budget_exceeded = true;
    } else {
        budget_exceeded = false;
    }

    $: if (timetracking.service_booking_id) getServiceBooking(timetracking);
    $: if (timetracking.client_id || timetracking.session_date)
        getServiceAgreement(timetracking);
    $: if (timetracking.session_date) getServiceAgreement(timetracking);

    function getAvailableSessionDuration() {
        jspa("/Participant/ServiceBooking", "getAvailableSessionDuration", {
            service_booking_id: timetracking.service_booking_id,
        })
            .then((result) => {
                available_session_duration =
                    result.result.available_session_duration;

                available_session_duration =
                    available_session_duration +
                    (timetracking.session_duration || 0);

                plan_manager_id = result.result.plan_manager_id;
                record_travelled_kilometers =
                    result.result.record_travelled_kilometers;
            })
            .catch(() => {});
    }

    function getServiceBooking(timetracking) {
        jspa("/Participant/ServiceBooking", "getParticipantServiceBooking", {
            id: timetracking.service_booking_id,
        })
            .then((result) => {
                serviceBooking = result.result;
            })
            .catch((error) => {
                console.log("Error fetching service booking:", error);
            });
    }

    function getServiceAgreement(timetracking) {
        jspa(
            "/Participant/ServiceAgreement",
            "listServiceAgreementsByParticipantId",
            {
                participant_id: timetracking.client_id,
            },
        )
            .then((result) => {
                service_agreement = result.result;
                hasActiveServiceAgreement =
                    checkIfHasActiveServiceAgreement(service_agreement);
            })
            .catch((error) => {
                console.error("Error fetching service agreements:", error);
            });
    }

    function checkIfHasActiveServiceAgreement(service_agreement) {
        return (
            service_agreement?.some((agreement) => agreement.is_active) || false
        );
    }

    function formatErrorMessage(error_message, index) {
        // the error_message is a string with semicolons.  Split it into an array and return the first element
        return error_message.split(";")[index];
    }

    // load services data here
    let stored_client_id = null;
    let serviceBookingList = [];
    let activeServiceBookings = [];

    // Reactive block to load services when client_id changes
    $: {
        if (timetracking.client_id && timetracking.client_id != stored_client_id) {
            timetracking.service_booking_id = null; // reset service_booking_id
            loadServices(timetracking.client_id);
        }
    }

    function loadServices(client_id) {
        stored_client_id = client_id;
        
        jspa("/Participant/ServiceBooking", "listServiceBookings", {
            participant_id: client_id,
        })
            .then((result) => {
                serviceBookingList = []; // clear the service list
                let serviceBookings = result.result;
                activeServiceBookings = [];
                hasDuplicate = false; // Flag to track if there's any duplicate service_id

                // Map to track the occurrence of each service_id
                let serviceIdCount = {};

                // First pass to count occurrences of each service_id
                serviceBookings.forEach((serviceBooking) => {
                    if (!serviceBooking.archived &&
                        serviceBooking.is_active &&
                        !isExpired(serviceBooking) &&
                        serviceBooking.service_agreement_is_active) {
                        if (serviceIdCount[serviceBooking.service_id]) {
                            serviceIdCount[serviceBooking.service_id]++;
                        } else {
                            serviceIdCount[serviceBooking.service_id] = 1;
                        }
                    }
                });

                // Check for duplicates
                for (let service_id in serviceIdCount) {
                    if (serviceIdCount[service_id] > 1) {
                        hasDuplicate = true;
                        break;
                    }
                }

                // Second pass to populate serviceBookingList
                serviceBookings.forEach((serviceBooking) => {
                    let options = {
                        option: serviceBooking.code,
                        value: serviceBooking.id,
                        selected: false,
                    };

                    if (serviceBooking.id == timetracking.service_booking_id) {
                        options.selected = true;
                    }

                    // Filter valid service bookings
                    if (
                        !serviceBooking.archived &&
                        serviceBooking.is_active &&
                        !isExpired(serviceBooking) &&
                        serviceBooking.service_agreement_is_active
                    ) {
                        activeServiceBookings.push(serviceBooking); // Store active service bookings
                        serviceBookingList.push(options);
                    }
                });

                // Sort the service bookings by name
                serviceBookingList.sort((a, b) =>
                    a.option.localeCompare(b.option)
                );

                // Automatically select a service if only one active service exists
                if (activeServiceBookings.length === 1) {
                    timetracking.service_booking_id = activeServiceBookings[0].id;
                }
            })
            .catch((error) => {
                console.error("Error loading services:", error);
            });
    }

    function isExpired(serviceBooking) {
        const start = new Date(serviceBooking.service_agreement_signed_date).getTime();
        const end = new Date(serviceBooking.service_agreement_end_date).getTime();
        const current = getDateOnlyTimestamp(new Date());

        // Ensure the current date is within the interval
        return current <= start || current >= end;
    }
</script>

{#if timetracking.error}
    <div class="rounded-md bg-red-50 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-red-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-red-800">
                    {formatErrorMessage(timetracking.error.error_message, 1)}
                </h3>
                <p class="text-sm text-red-700">
                    {formatErrorMessage(timetracking.error.error_message, 0)}
                </p>
                <p class="text-xs text-red-700 opacity-50">
                    {formatErrorMessage(timetracking.error.error_message, 2)}
                </p>

                {#if timetracking.error.related_billables}
                    <div class="mt-2 text-sm text-gray-700">
                        <p class="text-sm text-gray-700">
                            Please note the following billable items are also
                            affected by this error:
                        </p>

                        <ul role="list" class="list-disc space-y-1 pl-5">
                            {#each timetracking.error.related_billables as billable}
                                <li>
                                    <a
                                        href="/#/billables/{billable.timetracking_id}"
                                        >{billable.timetracking_id}</a
                                    >
                                </li>
                            {/each}
                        </ul>
                    </div>
                {/if}
            </div>
        </div>
    </div>
{/if}

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

<Role roles={["accounts", "admin"]}>
    <div class="flex-1 md:flex-none md:w-100">
        <StaffSelector bind:staff_id={timetracking.staff_id} />
    </div>
</Role>

<div class="flex flex-col md:flex-row gap-x-2">
    <div class="flex-1 md:flex-none md:w-50">
        <FloatingDate
            bind:value={timetracking.session_date}
            label="Date"
            {readOnly}
        />
    </div>

    <div class="flex-1">
        <ClientSelector
            bind:client_id={timetracking.client_id}
            bind:on_hold={client_on_hold}
            {readOnly}
        />
    </div>
</div>

{#if client_on_hold}
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-red-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    This client is on hold so billing is unavailable.
                </h3>
            </div>
        </div>
    </div>
{:else}
    {#if !hasDuplicate}
        <ServiceButtonGroup
            bind:service_booking_id={timetracking.service_booking_id}
            bind:serviceBookingList={serviceBookingList}
            {readOnly}
        />
    {/if}

    {#if timetracking?.client_id && !hasActiveServiceAgreement}
        <div class="rounded-md bg-red-50 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg
                        class="h-5 w-5 text-red-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        There are no active services for this participant.
                    </h3>
                </div>
            </div>
        </div>
    {/if}
    {#if timetracking.session_date > service_agreement.service_agreement_end_date || timetracking.session_date < service_agreement.service_agreement_signed_date}
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg
                        class="h-5 w-5 text-red-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        The date is outside of the service agreement period.
                    </h3>
                </div>
            </div>
        </div>
    {:else}

        {#if timetracking.staff_id && timetracking.client_id && timetracking.service_booking_id && timetracking.service_booking_id != "Choose service" && !hasDuplicate}
            <Container>
                <ServiceBooking bind:service_booking={serviceBooking} />
            </Container>

            {#if mode == "edit"}
                <Role roles={["admin"]}>
                    <PlanManagerSelector
                        bind:planmanager_id={timetracking.planmanager_id}
                        {readOnly}
                    />
                </Role>
            {/if}

            {#if available_session_duration > 0}
                <ClaimSelector
                    bind:claim_type={timetracking.claim_type}
                    bind:cancellation_reason={timetracking.cancellation_reason}
                    {readOnly}
                />

                <FloatingDurationSelect
                    label="Session Duration"
                    bind:value={timetracking.session_duration}
                    {readOnly}
                />
                {#if budget_exceeded}
                    <div class="text-center text-red-500 font-bold">
                        This will take you over budget. There is only {@html convertMinutesToHoursAndMinutes(
                            available_session_duration,
                        )} available.
                    </div>
                {/if}
            {:else}
                <div class="text-center text-red-500">
                    No available session duration for this service.
                </div>
            {/if}

            <!-- <FloatingInput
                label="Billing Summary"
                placeholder="eg: Developmental assessment conducted via video call."
                bind:value={timetracking.actual_travel_time}
                {readOnly}
            /> -->

            <Container>
                <div style="z-index:1">
                    <div class="mb-2 opacity-50 text-xs">Case Note</div>
                    <div class="flex items-start px-2 mb-2">
                        <div class="flex h-6 items-center">
                            <input
                                id="comments"
                                aria-describedby="comments-description"
                                name="comments"
                                type="checkbox"
                                bind:checked={timetracking.internal}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                            />
                        </div>
                        <div class="ml-2 text-sm leading-6">
                            <label
                                for="comments"
                                class="font-medium text-gray-900"
                                >Internal Only</label
                            >
                            <span
                                id="comments-description"
                                class="text-gray-500 text-xs italic"
                                ><span class="sr-only">Internal Only </span> (Tick
                                to prevent stakeholders reading this case note)</span
                            >
                        </div>
                    </div>

                    <RTE bind:content={timetracking.notes} />
                </div>
            </Container>
        {/if}
    {/if}

    {#if hasDuplicate}
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-red-800">Service setup error detected:</h3>
                    <p class="text-sm text-red-800">Duplicate support items found. Please ask your manager to correct the support items for this participant to proceed with billing.</p>
                </div>
            </div>
        </div>
    {/if}
{/if}
