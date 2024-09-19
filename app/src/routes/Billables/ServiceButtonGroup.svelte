<script>
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";
    import { onMount } from "svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import { jspa } from "@shared/jspa.js";

    export let client_id;
    export let service_booking_id = null;
    export let readOnly = false;

    let stored_client_id = null;

    let serviceBookings = [];
    let serviceBookingList = [];

    $: console.log("Service Booking ID: " + service_booking_id);

    onMount(() => {
        if (client_id) {
            loadServices(client_id);
        }
    });

    $: {
        if (client_id && client_id != stored_client_id) {
            loadServices(client_id);
        }
    }

    function loadServices(client_id) {
        jspa("/Participant/ServiceBooking", "listServiceBookings", {
            participant_id: client_id,
        })
            .then((result) => {
                serviceBookingList = []; // clear the servicList
                service_booking_id = null; // clear the selected service
                serviceBookings = result.result;
                console.log(serviceBookings);
                let selected = false;

                serviceBookings.forEach((serviceBooking) => {
                    let options = {
                        option: serviceBooking.code,
                        value: serviceBooking.id,
                        selected: false,
                    };

                    if (serviceBooking.id == service_booking_id) {
                        options.selected = true;
                        selected = true;
                    }

                    if (
                        serviceBooking.archived != 1 &&
                        serviceBooking.is_active == 1
                    )
                        serviceBookingList.push(options);
                });

                // if (!selected) service_id = "Choose service"; // unset the selected client_id

                serviceBookingList.sort(function (a, b) {
                    const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });

                serviceBookingList = serviceBookingList;

                stored_client_id = client_id;

                // count the number of services with is_active
                const activeServiceBookings = serviceBookings.filter(
                    (serviceBooking) => serviceBooking.is_active,
                );
                // if only one service, automatically select it
                if (activeServiceBookings.length == 1) {
                    service_booking_id = activeServiceBookings[0].id;
                }
            })
            .catch((error) => {});
    }
</script>

{#if serviceBookingList.length > 0}
    <div class="p-2">
        <!-- Services -->

        <RadioButtonGroup
            on:change
            bind:value={service_booking_id}
            label="Services"
            options={serviceBookingList}
            {readOnly}
        />
    </div>
{/if}
