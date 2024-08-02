<script>
  import { slide } from "svelte/transition";
  import { onMount } from "svelte";
  import { ActionBarStore } from "./stores.js";

  $: action_bar = $ActionBarStore;

  function undo() {
    action_bar.undo();
  }

  function save() {
    action_bar.save();
  }

  function del() {
    action_bar.delete();
  }

  onMount(async () => {
    window.addEventListener("popstate", () => (action_bar.show = false));
  });

  // onMount(async () => {
  //     window.addEventListener('popstate', function(event) {
  //         action_bar.show = false;
  //     });
  // });
</script>

{#if action_bar.show}
  <div transition:slide={{ duration: 150 }} class="h-16 block"></div>
  <!-- <div class="p-10 lg:p-5"></div> -->
  <div class="fixed bottom-0 z-20 w-full block">
    <div
      transition:slide={{ duration: 150 }}
      class="  bg-white border-t-2 border-indigo-100"
      style="width:-webkit-fill-available"
    >
      <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="flex justify-between p-4">
          {#if action_bar.valid == false}
            <div class="rounded-md bg-yellow-50 p-2">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg
                    class="h-5 w-5 text-yellow-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-yellow-800">
                    Incomplete
                  </h3>
                </div>
              </div>
            </div>
          {/if}

          {#if action_bar.can_delete}<button
              on:click={() => del()}
              type="button"
              class="inline-flex w-auto justify-start rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-white sm:mr-3 sm:w-auto"
              >Delete</button
            >{:else}<span class="inline-flex w-auto"></span>{/if}
          <div class="flex gap-1">
            {#if action_bar.undo}<button
                on:click={() => undo()}
                type="button"
                class=" w-auto mr-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >Undo</button
              >{/if}

            <button
              on:click={() => save()}
              type="button"
              class=" w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
              >Save</button
            >
          </div>
        </div>
      </div>
    </div>
  </div>
{/if}
