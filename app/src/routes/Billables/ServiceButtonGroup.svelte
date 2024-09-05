<script>
    import { onMount } from "svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import { jspa } from "@shared/jspa.js";

    export let client_id;
    export let participant_service_id;
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
                console.log(services);
                
                let selected = false;

                services.forEach((service) => {
                    let options = {
                        option: service.service_code,
                        value: service.id,
                        selected: false,
                    };

                    if (service.id == participant_service_id) {
                        options.selected = true;
                        selected = true;
                    }

                    if (service.archived != 1) serviceList.push(options);
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

                // if only one service, automatically select it
                if (services.length == 1) {
                    participant_service_id = services[0].id;
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
            bind:value={participant_service_id}
            label="Services"
            options={serviceList}
            {readOnly}
        />
    </div>
{/if}

{#if false}
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
                This client's services are inactive.
            </h3>
        </div>
    </div>
</div>
{/if}
