<script>
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let services = [];

    jspa("/Service", "listServices", {}).then((result) => {
        services = result.result;
    });

    BreadcrumbStore.set({ path: [{ name: "Services" }] });

    // async function getRate(billing_code, location){
    //     return jspa("/SupportItem", "getSupportItemByNumber", {support_item_number:billing_code}).then((result)=>{
    //         let support_item = result.result;
    //         console.log(support_item[location])
    //         return support_item[location]
    //     })
    // }

    function addService() {
        push("/services/add");
    }
</script>

<div class="flex items-center mb-4">
    <div class="flex-auto">
        <h1
            class="text-2xl font-fredoka-one-regular px-4"
            style="color:#220055;"
        >
            Services
        </h1>
    </div>
    <button
        on:click={() => addService()}
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add</button
    >
</div>

<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each services as service, index (service.id)}
        <li
            in:slide={{ duration: 200 }}
            out:slide={{ duration: 200 }}
            class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {services.length -
                1 ==
            index
                ? 'rounded-b-lg'
                : ''}border-b border-gray-200 w-full {service.archived == 1
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
    {:else}
        <li
            class="px-4 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default"
        >
            No services found.
        </li>
    {/each}
</ul>
