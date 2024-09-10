<script>
    import client_forms from "./client_forms.json";
    import forms from "./forms.json";
    import { push } from "svelte-spa-router";
    import { onMount, onDestroy } from "svelte";
    import { BreadcrumbStore, RolesStore } from "@shared/stores.js";
    import { getClient } from "@shared/api.js";
    import { haveCommonElements } from "@shared/utilities.js";

    export let params;
    let client_id = params.client_id;
    let client;
    let client_form_list = [];

    $: rolesStore = $RolesStore;

    onMount(async () => {
        client = await getClient(client_id);

        BreadcrumbStore.set({
            path: [
                { name: "Clients", url: "/clients" },
                { name: client.name, url: "/clients/" + client.id },
            ],
        });

        const filtered = client_forms.find(
            (form) => form.client_id == client.id,
        );
        if (filtered) {
            client_form_list = filtered.forms.map((form) => {
                return forms.find((f) => f.component == form);
            });
        }
    });
</script>

{#if haveCommonElements(rolesStore, ["house.lead", "house"])}
    <ul role="list" class="divide-y divide-gray-100">
        {#each client_form_list as form}
            <!-- Current: "bg-gray-50 text-indigo-600", Default: "text-gray-700 hover:text-indigo-600 hover:bg-gray-50" -->
            <li
                class="relative flex justify-between py-2 px-5 text-gray-700 hover:text-indigo-600 hover:bg-gray-50"
            >
                <div
                    on:click={() =>
                        push(
                            "/clients/" +
                                client.id +
                                "/forms/" +
                                form.component,
                        )}
                    class="min-w-0 flex-auto"
                >
                    <p class="text-sm font-semibold leading-6">
                        <span class="absolute inset-x-0 -top-px bottom-0"
                        ></span>
                        {form.label}
                    </p>
                    <p class=" text-xs text-gray-500">
                        {form.contents}
                    </p>
                </div>

                <div class="flex items-center justify-end gap-x-4 flex-none">
                    <svg
                        class="h-5 w-5 flex-none text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </li>
        {:else}
            <li class="relative flex justify-between py-2 px-5 text-gray-700">
                No forms have been created for this client
            </li>
        {/each}
    </ul>
{/if}
