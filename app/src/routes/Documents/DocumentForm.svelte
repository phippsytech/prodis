<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
  import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
  import Container from "@shared/Container.svelte";
  import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";
  import { jspa } from "@shared/jspa";

  export let document;
  export let selectedParticipant = [];  // Coming from edit.svelte
  let participantList = [];
  let participantsLoaded = false;
  let participantIds = [];  // To hold selected participant ids

  // Fetch participants list (clients)
  jspa("/Participant", "listClients", {}).then((result) => {
    let clients = result.result;
    clients.forEach((client) => {
      if (client.archived != 1) {
        participantList.push({
          option: client.client_name,
          value: client.client_id,
        });
      }
    });
    participantList.sort((a, b) => a.option.localeCompare(b.option));
    participantsLoaded = true;  // Set this when participants are fully loaded
  });

  // Sync selectedParticipant with participantIds after both are available
  $: if (participantsLoaded) {
    selectedParticipant = [...selectedParticipant];
  }

</script>

<!-- Document Form Fields -->
<FloatingInput
  label="Name"
  bind:value={document.name}
  placeholder="eg: First Aid"
  autofocus
/>
<FloatingTextArea
  label="Description"
  bind:value={document.description}
  placeholder="Provide a short description of the document so people understand what needs to be supplied.  (Optional)"
/>

<Container>
  <div class="text-sm font-medium mb-2">Collect Issuance/Expiry Date?</div>
  <div class="mb-2">
    <RadioButtonGroup
      options={[
        { value: "issued", option: "Issued" },
        { value: "expires", option: "Expiry" },
        { value: "do_not_collect", option: "Do not collect" },
      ]}
      bind:value={document.date_collection_option}
    />
  </div>

  {#if document.date_collection_option == "issued"}
    <div class="mb-2">
      <FloatingInput
        label="Years until expiry"
        bind:value={document.years_until_expiry}
        placeholder="eg: 1"
      />
    </div>
  {/if}
</Container>

<Container>
  <div class="text-sm font-medium mb-2">
    Collect this document for selected Participants?
  </div>

  <!-- Render CheckboxButtonGroup only if participants are fully loaded -->
  {#if participantsLoaded}
    <CheckboxButtonGroup options={participantList} bind:values={selectedParticipant} />
  {:else}
    <p>Loading participants...</p>
  {/if}
</Container>
