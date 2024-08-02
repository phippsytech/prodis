<script>
    import { push } from "svelte-spa-router";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import StakeholderForm from "./Form.svelte";
    import { jspa } from "@shared/jspa.js";
    import { StafferStore } from "@shared/stores.js";

    export let params;
    let client = {};

    let stakeholder = {};
    stakeholder.client_id = params.client_id;

    let breadcrumbs_path = [];
    jspa("/Client", "getClient", { id: params.client_id }).then((result) => {
        client = result.result;

        breadcrumbs_path = [
            { url: "/clients", name: "Clients" },
            { url: "/clients/" + params.client_id, name: client.name },
        ];
    });

    function addStakeholder() {
        jspa("/Client/Stakeholder", "addStakeholder", stakeholder)
            .then((result) => {
                push("/clients/" + params.client_id);
            })
            .catch(() => {});
    }
</script>

<StakeholderForm {stakeholder} />

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addStakeholder()} label="Add" />
</div>
