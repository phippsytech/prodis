<script>
    import { onMount } from "svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import { jspa } from "@shared/jspa.js";
    import { getDateOnlyTimestamp } from "@shared/utilities.js";

    export let client_id;
    export let service_booking_id = null;
    export let readOnly = false;
    export let hasDuplicate = false;

    let stored_client_id = null;
    let serviceBookings = [];
    let serviceBookingList = [];
    let activeServiceBookings = [];

    onMount(() => {
        if (client_id) {
            loadServices(client_id);
        }
    });

    // Reactive block to load services when client_id changes
    $: {
        if (client_id && client_id != stored_client_id) {
            loadServices(client_id);
        }
    }

    // Reactive block to check for duplicates when service_booking_id changes
    $: {
        if (service_booking_id) {
            const selectedService = activeServiceBookings.find(
                (serviceBooking) => serviceBooking.id == service_booking_id
            );

            if (selectedService) {
                // Check if any other active service booking has the same service_id
                const duplicates = activeServiceBookings.filter(
                    (serviceBooking) =>
                        serviceBooking.service_id == selectedService.service_id &&
                        serviceBooking.id != selectedService.id
                );

                // Set the hasDuplicate flag if duplicates are found
                hasDuplicate = duplicates.length > 0;
            }
        }
    }

    function loadServices(client_id) {
        jspa("/Participant/ServiceBooking", "listServiceBookings", {
            participant_id: client_id,
        })
            .then((result) => {
                serviceBookingList = []; // clear the service list
                serviceBookings = result.result;
                activeServiceBookings = [];

                serviceBookings.forEach((serviceBooking) => {
                    let options = {
                        option: serviceBooking.code,
                        value: serviceBooking.id,
                        selected: false,
                    };

                    if (serviceBooking.id == service_booking_id) {
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

                stored_client_id = client_id;

                // Automatically select a service if only one active service exists
                if (activeServiceBookings.length === 1) {
                    service_booking_id = activeServiceBookings[0].id;
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

    function handleChange() {
        // Handle the radio button group change event
    }
</script>

{#if serviceBookingList.length > 0}
    <div class="p-2">
        <!-- Services -->
        <RadioButtonGroup
            on:change={handleChange}
            bind:value={service_booking_id}
            label="Services"
            options={serviceBookingList}
            {readOnly}
        />
    </div>
{/if}
