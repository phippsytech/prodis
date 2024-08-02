<script>
  import { RolesStore, StafferStore, HouseStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { ArrowLeftIcon } from "heroicons-svelte/24/outline";
  import NavBar from "./NavBar/NavBar.svelte";
  import SideBar from "./SideBar.svelte";

  let show_sidebar = false;

  function toggleShow() {
    show_sidebar = !show_sidebar;
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

  function resetHouse() {
    HouseStore.update((currentData) => {
      return {};
    });
  }
</script>

<div class="hidden lg:block">
  <NavBar bind:show_sidebar />
</div>

{#if show_sidebar}
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

        <SideBar />
      </div>
    </div>
  </div>
{/if}

<!-- Static sidebar for desktop -->
<div
  class="hidden lg:fixed lg:inset-y-0 z-10 lg:flex lg:w-72 lg:flex-col mt-16"
>
  <SideBar />
</div>
