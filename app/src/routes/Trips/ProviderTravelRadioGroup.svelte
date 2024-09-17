<script>
    import { createEventDispatcher } from "svelte";
    import RadioGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte";
    import { jspa } from "@shared/jspa.js";

    export let client_id = null;
    export let service;
    export let service_booking_id = null;
    export let support_item_count = 0;

    let stored_client_id = null;

    let services = [];
    let serviceList = [];

    const dispatch = createEventDispatcher();

    $: {
        // if (client_id && client_id != stored_client_id) {
        if (client_id) {
            loadServices(client_id);
        }
    }

    $: {
        if (client_id == null) {
            service = {};
            serviceList = []; // clear the serviceList
            services = []; // clear the services
            support_item_count = 0;
        }
    }

    $: {
        support_item_count = services.length;
    }

    export function loadServices(client_id) {
        // client_id is not set, just return
        if (client_id === null) return;

        jspa("/Participant/ServiceBooking", "listProviderTravelByClientId", {
            client_id: client_id,
        })
            .then((result) => {
                // service_id = null;
                service = {};
                serviceList = []; // clear the servicList
                services = result.result;

                let selected = false;

                services.forEach((service) => {
                    let options = {
                        option: service.code,
                        value: service.service_booking_id,
                        selected: false,
                    };

                    if (service.service_booking_id == service_booking_id) {
                        options.selected = true;
                        selected = true;
                        service = service;
                    }

                    serviceList.push(options);
                });

                serviceList.sort(function (a, b) {
                    const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });

                stored_client_id = client_id;

                if (services.length == 1) {
                    // if only one service, automatically select it
                    service = services[0];
                    service_booking_id = services[0].service_booking_id;
                }

                dispatch("change", { value: service_booking_id });
            })
            .catch((error) => {
                console.log(error);
            });
    }

    function selectService(service_booking_id) {
        const item = services.find(
            (item) => item.service_booking_id === service_booking_id,
        );
        return item ? item : null;
    }

    // Watch for changes in selectedBillingCode and update the travel code accordingly
    $: {
        if (service_booking_id) {
            service = selectService(service_booking_id);
        }
    }

    function handleChange() {
        dispatch("change", { value: service_booking_id });
    }
</script>

<RadioGroup
    on:click
    on:change={handleChange}
    bind:value={service_booking_id}
    options={serviceList}
/>
