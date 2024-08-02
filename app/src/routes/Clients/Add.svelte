<script>
    import { push } from "svelte-spa-router";

    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    let client = {};
    client.date_of_birth = "2000-01-01"; // setting default date of Birth to 1/1/2000

    let dob_start_year = new Date().getFullYear() - 110;
    let dob_end_year = new Date().getFullYear();

    function addClient() {
        jspa("/Client", "addClient", client).then((result) => {
            client = result.result;
            push("/clients/" + client.id);
        });
    }

    let breadcrumbs_path = [{ url: "/clients", name: "Clients" }];
</script>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Client Details
</h1>
<FloatingInput
    bind:value={client.name}
    label="Client Name"
    placeholder="eg: Chris Person"
/>
<FloatingDateSelect
    bind:value={client.date_of_birth}
    label="Date of Birth"
    start_year={dob_start_year}
    end_year={dob_end_year}
/>
<FloatingInput
    bind:value={client.phone_number}
    label="Phone Number"
    placeholder="eg: XXXX XXX XXX"
/>
<FloatingInput
    bind:value={client.ndis_number}
    label="NDIS Number"
    placeholder="eg: XXXXXXXXXX"
/>
<FloatingInput
    bind:value={client.mailing_address}
    label="Address"
    placeholder="eg: 1 Someplace St, Adelaide SA 1234"
/>
<FloatingInput
    bind:value={client.service_provision_location}
    label="Service Provision Location"
    placeholder="eg: 1 Someplace St, Adelaide SA 1234"
/>
<FloatingInput
    bind:value={client.referrer}
    label="Referrer"
    placeholder="eg: Alex Citizen"
/>

<div class="flex justify-between">
    <span></span>
    <Button label="Next" on:click={() => addClient()} />
</div>
