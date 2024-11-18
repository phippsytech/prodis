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
    <div class="px-4 pt-1 pb-3 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default text-sm">
      No ended service agreements found.
    </div>




  {/if}
</div>
