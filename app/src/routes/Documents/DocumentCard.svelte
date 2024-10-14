<script>
  import { push } from "svelte-spa-router";
  export let document = {};
</script>

<div
  on:click
  class="relative flex flex-col h-full space-x-3 rounded-lg border border-gray-300 bg-white px-4 py-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:bg-indigo-600 hover:text-white group"
>
  <div class="min-w-0 flex-1">
    <span class="focus:outline-none">
      <span class="absolute inset-0" aria-hidden="true"></span>
      <p class="text-sm font-medium text-gray-900 group-hover:text-white">
        {document.name}
      </p>
      {#if document.date_collection_option == "issued"}
        <div class="text-xs">
          Collect Issue Date <span class="font-light"
            >(expires in {document.years_until_expiry} years)</span
          >
        </div>
      {/if}
      {#if document.date_collection_option == "expires"}
        <div class="text-xs">Collect Expiry Date</div>
      {/if}
      {#if document.collect_from_therapist}<div class="text-xs">
          Therapist: {document.collect_from_therapist}
        </div>{/if}
      {#if document.collect_from_sil}<div class="text-xs">
          SIL: {document.collect_from_sil}
        </div>{/if}
    </span>
  </div>
</div>
