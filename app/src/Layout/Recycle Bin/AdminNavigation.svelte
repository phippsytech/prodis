<script>
  import AdminNavList from "./AdminNavList.svelte";
  import { push, pop, replace } from "svelte-spa-router";
  import {
    AppData,
    RolesStore,
    StafferStore,
    HouseStore,
  } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { ArrowsRightLeftIcon } from "heroicons-svelte/24/outline";
  import { ArrowLeftIcon } from "heroicons-svelte/24/outline";

  let show = false;

  function toggleShow() {
    show = !show;
  }

  // function selectAdmin(false){
  //     HouseStore.update((currentData)=>{
  //         return {};
  //     });
  // }

  function selectAdmin(value) {
    AppData.update((currentData) => {
      let newData = JSON.parse(currentData);
      newData["admin"] = value;
      return JSON.stringify(newData);
    });
  }
</script>

{#if show}
  <!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
  <div class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
    <!--
          Off-canvas menu backdrop, show/hide based on off-canvas menu state.
    
          Entering: "transition-opacity ease-linear duration-300"
            From: "opacity-0"
            To: "opacity-100"
          Leaving: "transition-opacity ease-linear duration-300"
            From: "opacity-100"
            To: "opacity-0"
        -->
    <div class="fixed inset-0 bg-gray-900/80"></div>

    <div class="fixed inset-0 flex">
      <!--
            Off-canvas menu, show/hide based on off-canvas menu state.
    
            Entering: "transition ease-in-out duration-300 transform"
              From: "-translate-x-full"
              To: "translate-x-0"
            Leaving: "transition ease-in-out duration-300 transform"
              From: "translate-x-0"
              To: "-translate-x-full"
          -->
      <div class="relative mr-16 flex w-full max-w-xs flex-1">
        <!--
              Close button, show/hide based on off-canvas menu state.
    
              Entering: "ease-in-out duration-300"
                From: "opacity-0"
                To: "opacity-100"
              Leaving: "ease-in-out duration-300"
                From: "opacity-100"
                To: "opacity-0"
            -->
        <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
          <button
            on:click={() => toggleShow()}
            type="button"
            class="-m-2.5 p-2.5"
          >
            <span class="sr-only">Close sidebar</span>
            <svg
              class="h-6 w-6 text-white"
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

        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div
          class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-2"
        >
          <div class="flex h-16 shrink-0 items-center">
            <span class="font-bold">Admin</span><br />
            <span class="text-xs"
              ><a
                class="text-blue-500 font-lighter"
                href="/#/"
                on:click={() => selectAdmin(false)}
                ><ArrowsRightLeftIcon class="w-5 h-5 inline" /></a
              ></span
            >
          </div>
          <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
              <li>
                <AdminNavList />
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
{/if}

<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col">
  <!-- Sidebar component, swap this element with another sidebar if you like -->
  <div
    class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6"
  >
    <div class="flex h-16 shrink-0 items-center">
      <span class="font-bold">Admin</span><br />
      <span class="text-xs"
        ><a
          class="text-blue-500 font-lighter"
          href="/#/"
          on:click={() => selectAdmin(false)}
          ><ArrowsRightLeftIcon class="w-5 h-5 inline" /></a
        ></span
      >
    </div>
    <nav class="flex flex-1 flex-col">
      <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
          <AdminNavList />
        </li>
      </ul>
    </nav>
  </div>
</div>

<div
  class="w-full z-40 fixed flex items-center gap-x-6 bg-white px-4 py-2 shadow-sm sm:px-6 lg:hidden"
>
  <button
    on:click={() => toggleShow()}
    type="button"
    class="-m-2.5 p-2.5 text-gray-700 lg:hidden"
  >
    <span class="sr-only">Open sidebar</span>
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
        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
      />
    </svg>
  </button>
  <div class="flex-1 text-sm font-semibold leading-6 text-gray-900">Admin</div>
</div>
<div class="h-8 mb-2 lg:hidden z-0"></div>
