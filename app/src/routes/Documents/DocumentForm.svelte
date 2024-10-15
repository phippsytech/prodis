<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
  import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
  import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
  import Container from "@shared/Container.svelte";
  import { jspa } from "@shared/jspa";
  import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";
  import { debounce } from "lodash-es";

  export let document;
  export let selectedParticipant = [];
  let participantIds = [];
  let clients = [];
  let participantList = [];
  let requestCounter = 0;


  jspa("/Participant", "listClients", {}).then((result) => {
    clients = result.result;

    clients.forEach((client) => {
      if (client.archived != 1)
        participantList.push({
          option: client.client_name,
          value: client.client_id,
        });
    });

    participantList.sort((a, b) => a.option.localeCompare(b.option));

    participantList = participantList;  
    
  });

  $: {
    if (participantIds.length > 0) {
      selectedParticipant = [...participantIds];
    }

    if (selectedParticipant.length > 0) {
      participantIds = [...selectedParticipant];

      console.log('participantIds', participantIds);
      
    }
  }



</script>

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
        autofocus
      />
    </div>
  {/if}
  <!-- <Toggle bind:value={document.collect_completion} label_on="Collect Completion Date" label_off="Collect Completion Date" />
<Toggle bind:value={document.collect_expiry} label_on="Collect Expiry" label_off="Collect Expiry" /> -->
</Container>

<Container>
  <div class="text-sm font-medium mb-2">
    Collect this document for selected Participants?
  </div>
  <CheckboxButtonGroup options={participantList} bind:values={participantIds} />
</Container>


