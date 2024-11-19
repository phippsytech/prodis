<script>
  import Container from "@shared/Container.svelte";
  import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { formatDate } from "@shared/utilities.js";
  import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

  let participant_id = 580;

  BreadcrumbStore.set({
    path: [{ url: null, name: "SIL" }],
  });

  let summary = [];

  jspa("/SIL/House/Form", "getParticipantFormSummary", {
    participant_id: participant_id,
  }).then((result) => {
    summary = result.result;
  });
</script>

<FloatingSelect
  on:change
  bind:value={participant_id}
  label="Participant"
  instruction="Choose participant"
  options={[{ option: "Cristian Salisbury", value: 580 }]}
  hideValidation={true}
/>

<Container>
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    SIL Participant Form Summary
  </div>

  <div class="mb-2">
    {#each summary as item}
      <div class="ml-4">
        {formatDate(item.date)}
        {item.report_type} ({item.count})
      </div>
    {/each}
  </div>
</Container>
