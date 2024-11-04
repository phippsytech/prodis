<script>
  import { makeUniqueId } from "@shared/PhippsyTech/svelte-ui/js/base.js";
  import { slide } from "svelte/transition";
  import { onMount, onDestroy } from "svelte";
  import { websocketStore, manageSubscription } from "@app/websocketStore.js";

  let pulse = [];

  let unsubscribeFromIsConnected;
  let unsubscribeFromOnMessage;

  onMount(() => {
    unsubscribeFromIsConnected = websocketStore.isConnected.subscribe(
      (connected) => {
        if (connected) {
          unsubscribeFromOnMessage = websocketStore.onMessage.subscribe(
            (value) => {
              try {
                let obj = JSON.parse(value);
                if (obj) {
                  obj.id = makeUniqueId();
                  pulse = [obj, ...pulse];
                  if (pulse.length > 10) {
                    pulse.pop();
                  }
                }
              } catch (error) {
                console.error("Error parsing JSON:", error);
              }
            }
          );
          manageSubscription("pulse", "subscribe");
        }
      }
    );
  });

  onDestroy(() => {
    manageSubscription("pulse", "unsubscribe");
    unsubscribeFromIsConnected && unsubscribeFromIsConnected();
    unsubscribeFromOnMessage && unsubscribeFromOnMessage();
  });
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Pulse
</div>

<!-- <button on:click={subscribe}>Subscribe</button>

<button on:click={sendMessage}>Send Message</button> -->

<section class="mt-12">
  <ol class="mt-2 divide-y divide-gray-200 text-sm leading-6 text-gray-500">
    {#each pulse as beat (beat.id)}
      <li
        in:slide={{ duration: 100 }}
        out:slide={{ duration: 100 }}
        class="py-4 sm:flex"
      >
        <div class="w-28 flex-none">
          <span class="font-light">{beat.action}</span>
        </div>
        <pre class="text-xs text-gray-900">{JSON.stringify(beat, null, 2)}</pre>
        <p class="mt-2 flex-auto sm:mt-0"></p>
      </li>
    {:else}
      <li class="py-4 sm:flex">
        <p class="mt-2 flex-auto sm:mt-0">Waiting</p>
      </li>
    {/each}
  </ol>
</section>
