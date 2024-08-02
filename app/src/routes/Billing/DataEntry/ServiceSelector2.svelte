<script>
    import { onMount } from "svelte";

    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

    // import RadioButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte';

    import { jspa } from "@shared/jspa.js";

    export let client_id;
    export let service_id;
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
        jspa("/Participant/Service", "listParticipantServicesByParticipantId", {
            participant_id: client_id,
        })
            .then((result) => {
                serviceList = []; // clear the servicList
                services = result.result;

                let selected = false;

                services.forEach((service) => {
                    let options = {
                        option: service.service_code,
                        value: service.service_id,
                        selected: false,
                    };

                    if (service.service_id == service_id) {
                        options.selected = true;
                        selected = true;
                    }

                    if (service.archived != 1) serviceList.push(options);
                });

                if (!selected) service_id = "Choose service"; // unset the selected client_id

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
                    service_id = services[0].service_id;
                }
            })
            .catch((error) => {});
    }
</script>

<!-- Services -->
<FloatingSelect
    on:change
    bind:value={service_id}
    label="Services"
    instruction="Choose service"
    options={serviceList}
    hideValidation={true}
    {readOnly}
/>
