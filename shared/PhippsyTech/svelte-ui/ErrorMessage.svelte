<script>
  export let message = "";
  export let visible = false;
</script>

{#if visible}
  <div class="text-xs bg-red-500 text-white px-3 py-1 rounded-b-md mb-2">
    {message}
  </div>
{/if}