<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
  import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
  import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
  import Container from "@shared/Container.svelte";

  export let document;
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
    Collect this document from Therapists?
  </div>
  <RadioButtonGroup
    options={[
      { value: "required", option: "Required" },
      { value: "optional", option: "Optional" },
      { value: "do_not_collect", option: "Do not collect" },
    ]}
    bind:value={document.collect_from_therapist}
  />
</Container>

<Container>
  <div class="text-sm font-medium mb-2">Collect this document from SIL?</div>
  <RadioButtonGroup
    options={[
      { value: "required", option: "Required" },
      { value: "optional", option: "Optional" },
      { value: "do_not_collect", option: "Do not collect" },
    ]}
    bind:value={document.collect_from_sil}
  />
</Container>
