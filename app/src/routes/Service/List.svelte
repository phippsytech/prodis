<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import Filter from "@shared/PhippsyTech/svelte-ui/Filter.svelte";
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";

    let services = [];
    let serviceList = [];
    let showArchived = false;
    let filters = [{ label: "archived", enabled: false }];
    export let search = "";

    onMount(() => {
        jspa("/Service", "listServices", {}).then((result) => {
            console.log(result.result);
            services = result.result;
        });
    });

    $: {
        showArchived = filters.find((f) => f.label === "archived").enabled;

        if (!showArchived) {
            serviceList = services.filter((service) => service.archived != 1);
        } else {
            serviceList = services;
        }

        if (search.length > 0) {
            serviceList = serviceList.filter(
                (service) =>
                    (service.name &&
                        service.name
                            .toLowerCase()
                            .includes(search.toLowerCase())) ||
                    (service.code &&
                        service.code
                            .toLowerCase()
                            .includes(search.toLowerCase())),
            );  
        }
    }
</script>

<!-- <div class="bg-white px-3 rounded-md pb-1">
    <Filter bind:filters />
</div> -->

<div class="relative flex items-center mt-5">
    <div class="h-16 shrink-0 items-center w-full bg-white rounded-md">
        <ul
            class="bg-white rounded-lg border border-gray-200 w-full text-gray-900 items-center justify-center"
        >
            {#each serviceList as service, index (service.id)}
                <div>
                    <li
                        in:slide={{ duration: 200 }}
                        out:slide={{ duration: 200 }}
                        class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {services.length -
                            1 ==
                        index
                            ? 'rounded-b-lg'
                            : ''}border-b border-gray-200 w-full {service.archived ==
                        1
                            ? 'text-gray-400 cursor-default'
                            : ''}"
                    >
                        <button
                            on:click={() => push("/services/" + service.id)}
                            class="w-full h-full text-left"
                        >
                            <div class="font-bold">{service.name}</div>
                            <div class="text-xs font-lighter">
                                <span class="white-space-no-wrap"
                                    ><b>Code:</b> {service.code} &nbsp;
                                </span>
                                <span class="white-space-no-wrap"
                                    ><b>Rate:</b> ${service.rate}/hr &nbsp;
                                </span>
                            </div>
                        </button>
                    </li>
                </div>
            {:else}
                <li
                    class="px-4 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default"
                >
                    No services found.
                </li>
            {/each}
        </ul>
    </div>
</div>
