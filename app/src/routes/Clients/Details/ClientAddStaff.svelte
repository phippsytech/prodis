<script>
    import { push } from "svelte-spa-router";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import { jspa } from "@shared/jspa.js";

    export let params;

    let client = {};
    let client_staffer = {};
    let breadcrumbs_path = [];

    jspa("/Client", "getClient", { id: params.id }).then((result) => {
        client = result.result;
        breadcrumbs_path = [
            { url: "/clients", name: "Clients" },
            { url: "/clients/" + params.id, name: client.name },
        ];
    });

    // get Staff List for Select
    let staff = [];
    jspa("/Staff", "listStaff", {}).then((result) => (staff = result.result));
    let staffList = [];
    $: {
        staff.forEach((staffer) => {
            if (staffer.archived != 1)
                staffList.push({ option: staffer.name, value: staffer.id });
        });
        staffList = staffList;
    }

    function addClientStaff() {
        client_staffer.client_id = client.id;
        jspa("/Client/Staff", "addClientStaffer", client_staffer).then(
            (result) => {
                push("/clients/" + client.id);
            },
        );
    }
</script>

<FloatingSelect
    bind:value={client_staffer.staff_id}
    label="Staffer"
    instruction="Choose staffer"
    options={staffList}
    hideValidation={true}
/>

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addClientStaff()} label="Add" />
</div>
