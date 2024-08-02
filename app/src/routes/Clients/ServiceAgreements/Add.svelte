<script>
    import Container from "@shared/Container.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import { jspa } from "@shared/jspa.js";

    export let client = {};

    let client_plan = {};

    function addClientPlan() {
        client_plan.client_id = client.id;
        jspa("/Client/Plan", "addClientPlan", client_plan).then((result) => {
            client_plan = result.result;
            window.location.reload(); // this is cheating for now
        });
    }
</script>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Plan</h1>
<FloatingDate bind:value={client_plan.start_date} label="Start Date" />
<FloatingDate bind:value={client_plan.end_date} label="End Date" />

<div class="flex justify-between">
    <span></span>
    <Button label="Add" on:click={() => addClientPlan()} />
</div>
