<script>
    import { onMount } from "svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    export let client_id = null;
    export let service_id;
    export let billing_code;
    export let support_item_count = 0;
    export let readOnly = false;

    let stored_client_id = null;

    let services = [];
    let serviceList = [];

    $: {
        if (client_id && client_id != stored_client_id) {
            // service_id = null;
            // billing_code = null;
            loadServices(client_id);
        }
    }

    $: {
        if (client_id == null) {
            service_id = null;
            billing_code = null;
            serviceList = []; // clear the serviceList
            services = []; // clear the services
            support_item_count = 0;
        }
    }

    $: {
        support_item_count = services.length;
    }

    function loadServices(client_id) {
        // client_id is not set, just return
        if (client_id === null) return;

        jspa("/Participant/ServiceBooking", "listProviderTravelByClientId", {
            client_id: client_id,
        })
            .then((result) => {
                serviceList = []; // clear the servicList
                services = result.result;

                let selected = false;

                services.forEach((service) => {
                    let options = {
                        option: service.code + " - " + service.billing_code,
                        value: service.billing_code,
                        selected: false,
                    };

                    if (service.billing_code == service_id) {
                        options.selected = true;
                        selected = true;
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

                serviceList = serviceList;

                stored_client_id = client_id;

                // if only one service, automatically select it
                if (services.length == 1) {
                    service_id = services[0].billing_code;
                }

                billing_code = findBillingCode(service_id);
                console.log(billing_code, service_id);
            })
            .catch((error) => {
                console.log(error);
            });
    }

    // Function to find the travel billing code based on the selected billing code
    function findBillingCode(billingCode) {
        const item = services.find((item) => item.billing_code === billingCode);
        return item ? item.travel_billing_code : null;
    }

    // Watch for changes in selectedBillingCode and update the travel code accordingly
    $: {
        billing_code = findBillingCode(service_id);
    }
</script>

{#if serviceList.length > 0}
    <FloatingSelect
        on:change
        bind:value={service_id}
        label="Billing Item"
        instruction="Select Billing Item"
        options={serviceList}
        hideValidation={true}
        readOnly={serviceList.length == 1}
    />
{/if}
