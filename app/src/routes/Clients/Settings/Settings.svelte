<script>
  import { jspa } from "@shared/jspa.js";
  import { onMount } from "svelte";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { getClient } from "@shared/api.js";
  import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
  import Container from "@shared/Container.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";

  export let params;

  let client_id = params.client_id;
  let client = {};
  let mounted = false;

  onMount(async () => {
    client = await getClient(client_id);

    if (client.sil_enabled == "1") client.sil_enabled = true;
    if (client.sil_enabled == "0") client.sil_enabled = false;

    mounted = true;
  });

  function save() {
    jspa("/Client", "updateClient", client).then((result) => {
      toastSuccess("Client updated");
    });
  }
</script>

{#if mounted}
  <Container>
    <h1 class="text-gray-700 text-1xl font-semibold mt-0">SIL Settings</h1>
    <Toggle
      bind:value={client.sil_enabled}
      label_on="Client enabled for SIL"
      label_off="Client not enabled for SIL"
    />
    {#if client.sil_enabled}
      <div class="my-2">
        <FloatingInput
          bind:value={client.sil_email}
          label="SIL Email"
          placeholder="eg: sil@crystelcare.com.au"
        />
      </div>
    {/if}
  </Container>
{/if}
