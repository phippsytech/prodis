<script>
  import ServiceAgreement from "./ServiceAgreement.svelte";
  import { flip } from "svelte/animate";
  import { slide } from "svelte/transition";
  import { derived } from "svelte/store";
  import { createEventDispatcher } from "svelte";

  export let ServiceAgreementStore;

  const dispatch = createEventDispatcher();

  // Derived store to filter only ended agreements
  const endedAgreements = derived(
    ServiceAgreementStore,
    ($ServiceAgreementStore) => {
      return $ServiceAgreementStore.filter((agreement) => !agreement.is_active);
    }
  );

  function handleRenewed() {
    dispatch("renewed");
  }
</script>

<div class="p-2 pb-0">
  <p class="text-xs px-1 pb-2 text-slate-500">
    These Service Agreements are no longer effective, due to being supeseded,
    expired or terminated.
  </p>
  {#if $endedAgreements.length > 0}
    {#each $endedAgreements as agreement, index (agreement.id)}
      <div
        animate:flip={{ duration: 350 }}
        in:slide={{ duration: 200 }}
        out:slide={{ duration: 200 }}
      >
        <ServiceAgreement
          service_agreement={agreement}
          {ServiceAgreementStore}
          is_ended
          on:renewed={handleRenewed}
        />
      </div>
    {/each}
  {:else}
    <div class="rounded-md text-slate-400 px-4 pb-2 bg-slate-50 pt-3">
      No ended service agreements found.
    </div>
  {/if}
</div>
