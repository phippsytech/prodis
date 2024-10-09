<script>
    
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import StaffSelector from "./StaffSelector.svelte";
    import ClientSelector from "@shared/ClientSelector.svelte";
    import Role from "@shared/Role.svelte";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import ServiceButtonGroup from "./ServiceButtonGroup.svelte";
    import NewTimeEntryForm from "./NewTimeEntryForm.svelte";
    import { getQueryParams } from "@shared/utilities.js";

    export let timetracking = {};
    export let budget_exceeded = false;
    export let available_session_duration = null;
    export let mode = "add";

    export let params;

    // flags
    let hasDuplicate;
    let invalidClientId = false;
    let readOnly = false;
    let client_on_hold = false;

    // session duration
    let plan_manager_id = null;
    let record_travelled_kilometers = false;

    // service booking variables
    let serviceBookingList = [];
    let activeServiceBookings = [];

    // stored variables
    let stored_client_id = null;
    let stored_session_date = null;
    let stored_service_id;

    let queryParams = getQueryParams();
  
    if (queryParams.hasOwnProperty('participant_id')) {
        timetracking.client_id = queryParams['participant_id'];
    }

    if (timetracking.invoice_batch) {
        readOnly = true;
    }

    onMount(() => {
        if (timetracking.service_booking_id) {
            getAvailableSessionDuration();
        }
        stored_service_id = timetracking.service_booking_id;
        
        if (timetracking.client_id) {
            loadServices(timetracking);
        }
    });

    $: if (timetracking.service_booking_id && timetracking.service_booking_id != stored_service_id) {
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

    function formatErrorMessage(error_message, index) {
        // the error_message is a string with semicolons.  Split it into an array and return the first element
        return error_message.split(";")[index];
    }

    // Reactive block to load services when client_id or sesstion_date changes
    $: {
        if (
            (timetracking.client_id && timetracking.client_id != stored_client_id) ||
            (timetracking.session_date && timetracking.session_date != stored_session_date)
        ) {
            loadServices(timetracking);
        }
    }

    function loadServices(timetracking) {
        stored_client_id = timetracking.client_id;
        stored_session_date = timetracking.session_date;
        serviceBookingList = [];
        activeServiceBookings = [];
        timetracking.service_booking_id = null; // reset service_booking_id
        hasDuplicate = false;

        jspa("/Participant/ServiceBooking", "listServiceBookings", {
            participant_id: timetracking.client_id,
        })
            .then((result) => {
                serviceBookingList = []; // clear the service list
                activeServiceBookings = [];

                let serviceBookings = result.result;
                hasDuplicate = false; // Flag to track if there's any duplicate service_id

                // Map to track the occurrence of each service_id
                let serviceIdCount = {};

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
                        serviceBooking.service_agreement_is_active
                    ) {
                        activeServiceBookings.push(serviceBooking); // Store active service bookings

                        if (!isExpired(serviceBooking)) {
                            serviceBookingList.push(options);
                        }
                    }
                });

                // Sort the service bookings by name
                serviceBookingList.sort((a, b) =>
                    a.option.localeCompare(b.option)
                );

                if (serviceBookingList.length === 1 && !timetracking.service_booking_id) {
                    timetracking.service_booking_id = serviceBookingList[0].value;
                }
            })
            .catch((error) => {
                console.error("Error loading services:", error);
            });
    }

    function isExpired(serviceBooking) {
        const start = new Date(serviceBooking.service_agreement_signed_date).getTime();
        const end = new Date(serviceBooking.service_agreement_end_date).getTime();
        const current = new Date(timetracking.session_date).getTime();

        // Ensure the current date is outside the interval (before start or after end)
        return current < start || current > end;
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

<!-- catch invalid url -->
{#if invalidClientId && queryParams.hasOwnProperty('participant_id')}
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
                    Your current permission level prevents you from doing this
                </h3>
            </div>
        </div>
    </div>
{:else}
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
                bind:is_not_valid={invalidClientId}
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
    {:else if hasDuplicate}
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
    {:else if activeServiceBookings.length > 0 && serviceBookingList.length === 0} 
        <!-- has active services but session date is outside the service agreement period. -->
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
    {:else if activeServiceBookings.length === 0 && serviceBookingList.length === 0 && timetracking.client_id}
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
    {:else}
        <ServiceButtonGroup
            bind:service_booking_id={timetracking.service_booking_id}
            bind:serviceBookingList={serviceBookingList}
            {readOnly}
        />

        <!-- time entry form -->
        <NewTimeEntryForm 
            bind:mode={mode}
            bind:budget_exceeded={budget_exceeded}
            bind:available_session_duration={available_session_duration}
            bind:timetracking={timetracking}
            {readOnly}/>
    {/if}
{/if}