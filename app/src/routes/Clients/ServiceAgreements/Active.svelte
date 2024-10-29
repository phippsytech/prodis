<script>
  import ServiceAgreement from "./ServiceAgreement.svelte";
  import { slide } from "svelte/transition";
  import { flip } from "svelte/animate";
  import { derived } from "svelte/store";

  export let ServiceAgreementStore;

  // Derived store to filter only active agreements
  const activeAgreements = derived(
    ServiceAgreementStore,
    ($ServiceAgreementStore) => {
      return $ServiceAgreementStore.filter((agreement) => agreement.is_active);
    }
  );
</script>

<div class="p-2 pb-0">
  {#if $activeAgreements.length > 0}
    {#each $activeAgreements as agreement, index (agreement.id)}
      <div
        animate:flip={{ duration: 350 }}
        in:slide={{ duration: 200 }}
        out:slide={{ duration: 200 }}
      >
        <ServiceAgreement
          service_agreement={agreement}
          {ServiceAgreementStore}
        />
      </div>
    {/each}
  {:else}
    <div class="rounded-md text-slate-400 px-4 pb-2 bg-slate-50 pt-3">
      No active service agreements found.
    </div>
  {/if}
</div>
