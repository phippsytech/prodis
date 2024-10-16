<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import SessionList from "./SessionList.svelte";
    import { onMount } from "svelte";
    import { getClient } from "@shared/api.js";

    export let params;

    let client_id = params.client_id;

    let client = {};

    onMount(async () => {
        client = await getClient(client_id);

        BreadcrumbStore.set({
            path: [
                { url: "/clients", name: "Clients" },
                { url: "/clients/" + client.id, name: client.name },
                { name: "Billables" },
            ],
        });

        // BreadcrumbStore.set({path:[
        //     {url:'/clients', name:'Clients'},
        //     {url:'/clients/'+client_id, name:client.name},
        // ]})

        // jspa("/TimeTracking", "listTimeTrackingByClientId", {client_id: client_id, billed: billed}).then((result)=>{
        //     timetracking = result.result;

        //     // Make a distinct list of service codes to use as a filter.
        //     service_codes = [...new Set(timetracking.map(item => item.service_code))];

        //     filterTimeTrackings();

        // }).catch(error=>timetracking=[])
    });
</script>

<div class="flex justify-between mb-4">
    <div
        class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
    >
        Billables for {client.name}
    </div>
    <button
        on:click={() => push(`/billables/add?participant_id=${client_id}`)}
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add Billable</button
    >
</div>

<SessionList {client_id} />
