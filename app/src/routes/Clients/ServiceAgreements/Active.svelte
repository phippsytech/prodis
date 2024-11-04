<script>
  import ServiceAgreement from "./ServiceAgreement.svelte";
  import { slide } from "svelte/transition";
  import { flip } from "svelte/animate";
  import { derived } from "svelte/store";
  import { createEventDispatcher } from "svelte";

  export let ServiceAgreementStore;

  const dispatch = createEventDispatcher();

  // Derived store to filter only active agreements and flag expiring ones
  const activeAgreements = derived(ServiceAgreementStore, ($ServiceAgreementStore) => {
    const today = new Date(); // Get the current date
    const threeDaysFromNow = new Date(today);
    threeDaysFromNow.setDate(today.getDate() + 42); // Calculate the date three days from now

    return $ServiceAgreementStore
      .filter((agreement) => agreement.is_active)
      .map((agreement) => {
        const endDate = new Date(agreement.service_agreement_end_date); // Parse the end date
        const is_expiring = endDate <= threeDaysFromNow; // Check if the end date is within 3 days

        return {
          ...agreement,
          is_expiring, // Add the is_expiring flag
        };
      });
  });

  function handleRenewed() {
    dispatch("renewed");
  }
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
          is_expiring={agreement.is_expiring}
          on:renewed={handleRenewed}
        />
      </div>
    {/each}
  {:else}
    <div class="rounded-md text-slate-400 px-4 pb-2 bg-slate-50 pt-3">
      No active service agreements found.
    </div>
  {/if}
</div>
