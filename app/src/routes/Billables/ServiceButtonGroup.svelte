<script>
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";
    import { onMount } from "svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import { jspa } from "@shared/jspa.js";

    export let client_id;
    export let service_booking_id;
    export let readOnly = false;

    let stored_client_id = null;

    let services = [];
    let serviceList = [];

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
        jspa(
            "/Participant/ServiceBooking",
            "listParticipantServiceBookingsByParticipantId",
            {
                participant_id: client_id,
            },
        )
            .then((result) => {
                serviceList = []; // clear the servicList
                service_booking_id = null; // clear the selected service
                services = convertFieldsToBoolean(result.result, ["is_active"]);

                let selected = false;

                services.forEach((service) => {
                    let options = {
                        option: service.service_code,
                        value: service.id,
                        selected: false,
                    };

                    if (service.id == service_booking_id) {
                        options.selected = true;
                        selected = true;
                    }

                    if (service.archived != 1 && service.is_active == 1)
                        serviceList.push(options);
                });

                // if (!selected) service_id = "Choose service"; // unset the selected client_id

                serviceList.sort(function (a, b) {
                    const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });

                serviceList = serviceList;

                stored_client_id = client_id;

                // count the number of services with is_active
                const activeServices = services.filter(
                    (service) => service.is_active,
                );
                // if only one service, automatically select it
                if (activeServices.length == 1) {
                    service_booking_id = activeServices[0].id;
                }
            })
            .catch((error) => {});
    }
</script>

{#if serviceList.length > 0}
    <div class="p-2">
        <!-- Services -->
        <RadioButtonGroup
            on:change
            bind:value={service_booking_id}
            label="Services"
            options={serviceList}
            {readOnly}
        />
    </div>
{/if}
