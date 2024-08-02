<script>
  import { push, pop, replace } from "@app/../node_modules/svelte-spa-router";

  import { HomeIcon } from "@app/../node_modules/heroicons-svelte/dist/24/outline";

  export let path = [];
  export let target = "";
  export let action = {};
</script>

<div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8">
  <nav
    class="pl-0 pr-2 py-3 mb-4 rounded-md w-full flex justify-between"
    style="background: #ffffff;"
  >
    <ol class="list-reset flex">
      <li>
        <a href="/#/" class="text-blue-600 hover:text-blue-700"
          ><HomeIcon class="w-5 h-5 " /></a
        >
      </li>

      {#if path.length > 0}
        {#each path as item}
          <li><span class="text-slate-500 mx-2">/</span></li>
          <li>
            <a href="/#{item.url}" class="text-blue-600 hover:text-blue-700"
              >{item.name}</a
            >
          </li>
        {/each}
      {/if}
      <li><span class="text-slate-500 mx-2">/</span></li>
      <li class="text-black font-bold">{target}</li>
    </ol>
    <span>
      {#if action != {}}
        <div on:click={action.event}>
          <svelte:component this={action.icon} class="inline h-5 w-5" />
        </div>
      {/if}
    </span>
  </nav>
</div>
