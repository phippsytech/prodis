<script>

  import { slide } from 'svelte/transition';
  let is_online = navigator.onLine;

  function handleOnline() {
    is_online = true;
  }

  function handleOffline() {
    is_online = false;
  }

  window.addEventListener('online', handleOnline);
  window.addEventListener('offline', handleOffline);

</script>

{#if !is_online}
<div class="border-l-4 border-yellow-400 bg-yellow-50 p-4" transition:slide={{x: -100}} >
    <div class="flex">
      <div class="flex-shrink-0">
        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="ml-3">
        <p class="text-sm text-yellow-700">
          <b>Check your connection to the internet.</b>
        </p>
      </div>
    </div>
  </div>
  {/if}