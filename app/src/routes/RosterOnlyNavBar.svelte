<script>
  import { StafferStore, HouseStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { ArrowsRightLeftIcon } from "heroicons-svelte/24/outline";

  let show_user = false;
  let show_menu = false;
  let hostname = window.location.hostname;
  let staging = false;
  if (hostname == "app.ndismate.staging.phippsy.tech") {
    staging = true;
  }

  $: stafferStore = $StafferStore;
  $: houseStore = $HouseStore;

  let staff_id = null;

  function setStaffId(new_staff_id) {
    if (new_staff_id != null || new_staff_id != staff_id) {
      jspa("/Staff", "getStaffer", { id: new_staff_id })
        .then((result) => {
          StafferStore.update((currentData) => {
            let newData = currentData;
            newData = result.result;
            return newData;
          });
        })
        .catch((error) => {});
    }
  }

  function toggleUser() {
    show_user = !show_user;
  }

  function toggleMenu() {
    show_menu = !show_menu;
  }

  function resetStaff() {
    StafferStore.update((currentData) => {
      return {};
    });
  }

  function resetHouse() {
    HouseStore.update((currentData) => {
      return {};
    });
  }
</script>

<nav class="bg-gray-800">
  <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
    <div class="relative flex h-16 items-center justify-between">
      <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
        <!-- Mobile menu button-->
        <button
          on:click={() => toggleMenu()}
          type="button"
          class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
          aria-controls="mobile-menu"
          aria-expanded="false"
        >
          <span class="sr-only">Open main menu</span>
          <!--
                Icon when menu is closed.
    
                Menu open: "hidden", Menu closed: "block"
              -->
          <svg
            class="block h-6 w-6"
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
          <!--
                Icon when menu is open.
    
                Menu open: "block", Menu closed: "hidden"
              -->
          <svg
            class="hidden h-6 w-6"
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
      <div
        class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start"
      >
        <div class="flex flex-shrink-0 items-center">
          <span class="text-white font-bold">{houseStore.name} House</span><br
          />
          <span class="text-xs"
            ><a
              class="text-blue-500 font-lighter"
              href="/#/"
              on:click={() => resetHouse()}
              ><ArrowsRightLeftIcon class="w-5 h-5 inline" /></a
            ></span
          >
          <!-- <img class="block h-8 w-auto lg:hidden" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
              <img class="hidden h-8 w-auto lg:block" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company"> -->
        </div>
        <div class="hidden sm:ml-6 sm:block">
          <div class="flex space-x-4">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->

            <a
              href="/#/roster"
              class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium"
              >Roster</a
            >
          </div>
        </div>
      </div>
      <div
        class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0"
      >
        <!-- <button type="button" class="rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
              <span class="sr-only">View notifications</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
              </svg>
            </button> -->

        <!-- Profile dropdown -->
        <div class="relative ml-3">
          <div>
            <button
              type="button"
              class="inline flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
              id="user-menu-button"
              aria-expanded="false"
              aria-haspopup="true"
            >
              <span class="text-white">{stafferStore.name}</span>

              <!-- <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt=""> -->
            </button>
          </div>

          {#if show_user}
            <!--
                Dropdown menu, show/hide based on menu state.
    
                Entering: "transition ease-out duration-100"
                  From: "transform opacity-0 scale-95"
                  To: "transform opacity-100 scale-100"
                Leaving: "transition ease-in duration-75"
                  From: "transform opacity-100 scale-100"
                  To: "transform opacity-0 scale-95"
              -->
            <div
              class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              role="menu"
              aria-orientation="vertical"
              aria-labelledby="menu-button"
              tabindex="-1"
            >
              <div class="py-1" role="none">
                <a
                  href="/#/profile"
                  class="text-gray-700 block px-4 py-2 text-sm"
                  role="menuitem"
                  tabindex="-1"
                  id="menu-item-2">Your Profile</a
                >
              </div>
            </div>
          {/if}
        </div>
      </div>
    </div>
  </div>

  {#if show_menu}
    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="sm:hidden" id="mobile-menu">
      <div class="space-y-1 px-2 pb-3 pt-2">
        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->

        <a
          href="/#/roster"
          class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium"
          >Roster</a
        >
      </div>
    </div>
  {/if}
</nav>
