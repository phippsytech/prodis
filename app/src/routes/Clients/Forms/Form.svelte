<script>
    import { onMount, onDestroy } from "svelte";
    import {
        ClientStore,
        BreadcrumbStore,
        StafferStore,
    } from "@shared/stores.js";
    import { getClient } from "@shared/api.js";
    import FormActions from "./FormActions.svelte";

    export let params;

    let client_id = params.client_id;
    let component_name = params.form;
    let component;
    let client = {};
    let form; // do not initialise this with an empty object or forms can not use their default settings.

    let valid;

    // Stores
    $: stafferStore = $StafferStore;

    onMount(async () => {
        client = await getClient(client_id);
        loadComponent(component_name);
        BreadcrumbStore.set({
            path: [
                { url: "/clients/", name: "Clients" },
                { url: "/clients/" + client.id, name: client.name },
            ],
        });
    });

    async function loadComponent(component_name) {
        component = (await import("./Form/" + component_name + ".svelte"))
            .default;
    }

    $: {
        if (component_name) {
            loadComponent(component_name);
        }
    }
</script>

{#if component}
    <svelte:component
        this={component}
        bind:form
        bind:staff_id={stafferStore.id}
        bind:participant_id={client.id}
        bind:valid
    />
    <FormActions bind:form bind:valid />
{:else}
    <div
        class="flex justify-center items-center h-full text-center text-gray-400"
    >
        <svg
            class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>
        Loading
    </div>
{/if}
