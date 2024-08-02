<script>
  import { SlideOverStore } from "./stores.js";
  import { fly, fade } from "svelte/transition";

  $: slideover = $SlideOverStore;

  let slideOverBackgroundElement;
  let slideOverElement;
  let timeoutId;

  function close() {
    slideover.show = false;
  }

  function closeSlideOver() {
    SlideOverStore.set({ show: false });
  }

  function action() {
    slideover.action();
    closeSlideOver();
  }

  function del() {
    slideover.delete();
    closeSlideOver();
  }

  function handleClick(event) {
    if (!slideOverElement.contains(event.target)) {
      closeSlideOver();
    }
  }

  $: {
    if (slideover.show) {
      // debounce the click event to prevent it from firing immediately
      clearTimeout(timeoutId);
      window.removeEventListener("click", handleClick);
      timeoutId = setTimeout(
        () => window.addEventListener("click", handleClick),
        300,
      );
    } else {
      window.removeEventListener("click", handleClick);
    }
  }
</script>

{#if slideover.show}
  <div
    class="relative z-20"
    aria-labelledby="slide-over-title"
    role="dialog"
    aria-modal="true"
  >
    <div
      transition:fade={{ duration: 350 }}
      class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    ></div>

    <div class="fixed inset-0 overflow-hidden">
      <div class="absolute inset-0 overflow-hidden">
        <div
          class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10"
        >
          <div
            bind:this={slideOverElement}
            class="pointer-events-auto w-screen max-w-md"
            in:fly={{ x: 1000, duration: 350 }}
            out:fly={{ x: 1000, duration: 350 }}
          >
            <div
              class="flex h-full flex-col overflow-y-scroll bg-white pt-6 shadow-xl"
            >
              <div class="px-4 sm:px-6">
                <div class="flex items-start justify-between">
                  <h2
                    class="text-base font-semibold leading-6 text-gray-900"
                    id="slide-over-title"
                  >
                    {slideover.label}
                  </h2>
                  <div class="ml-3 flex h-7 items-center">
                    <button
                      on:click={() => close()}
                      type="button"
                      class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                      <span class="sr-only">Close</span>
                      <svg
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M6 18L18 6M6 6l12 12"
                        />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              <div class="relative mt-6 flex-1 px-4 sm:px-6">
                <!-- Your content -->

                <slot name="content">
                  <svelte:component
                    this={slideover.component}
                    bind:props={slideover.props}
                  />
                </slot>
              </div>

              <div class="bg-gray-50 px-4 py-3 sm:px-6">
                <slot name="action">
                  <div class="flex justify-between">
                    {#if slideover.delete}
                      <button
                        on:click={() => del()}
                        type="button"
                        class="inline-flex w-auto justify-start rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-white sm:mr-3 sm:w-auto"
                        >Delete</button
                      >{:else}<span></span>{/if}
                    <div class="flex gap-1">
                      <button
                        on:click={() => closeSlideOver()}
                        type="button"
                        class="w-full sm:w-auto sm:mr-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                        >Cancel</button
                      >
                      {#if slideover.action}<button
                          on:click={() => action()}
                          type="button"
                          class="w-full sm:w-auto inline-flex justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                          >{slideover.action_label}</button
                        >{/if}
                    </div>
                  </div>
                </slot>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/if}
