<script>
    import { push } from "svelte-spa-router";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import StakeholderForm from "./Form.svelte";
    import { jspa } from "@shared/jspa.js";
    import { StafferStore } from "@shared/stores.js";

    export let params;
    let client = {};

    let stakeholder = {};

    let breadcrumbs_path = [];
    jspa("/Client", "getClient", { id: params.client_id }).then((result) => {
        client = result.result;
        breadcrumbs_path = [
            { url: "/clients", name: "Clients" },
            { url: "/clients/" + params.client_id, name: client.name },
        ];
    });

    jspa("/Client/Stakeholder", "getStakeholder", {
        id: params.stakeholder_id,
    }).then((result) => {
        stakeholder = result.result;
    });

    function update() {
        jspa("/Client/Stakeholder", "updateStakeholder", stakeholder)
            .then((result) => {
                push("/clients/" + params.client_id);
            })
            .catch(() => {});
    }

    function makeRepresentative(stakeholder_id) {
        jspa("/Client/Stakeholder", "makeRepresentative", {
            client_id: client.id,
            stakeholder_id: stakeholder.id,
        })
            .then((result) => {
                stakeholder.representative = 1;
                push("/clients/" + params.client_id);
            })
            .catch((error) => {});
    }
</script>

{#if stakeholder.representative == 1}
    <div
        class="bg-blue-100 rounded-lg py-5 px-6 mb-4 text-base text-blue-700 mb-3"
        role="alert"
    >
        <b>{stakeholder.name} is the representative.</b><br />
        <span class="text-xs"
            >To change this, make another stakeholder the representative.</span
        >
    </div>
{:else}
    <div
        class="bg-blue-100 rounded-lg py-5 px-6 mb-4 text-base text-blue-700 mb-3 text-center"
        role="alert"
    >
        <button
            on:click={() => makeRepresentative()}
            type="button"
            class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
            >Make {stakeholder.name} Representative</button
        >
        <div class="text-xs mt-2">
            Note: This will replace the current representative.
        </div>
    </div>
{/if}

<StakeholderForm bind:stakeholder />

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => update()} label="Update" />
</div>
