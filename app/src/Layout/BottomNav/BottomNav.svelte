<script>
  import { onMount } from "svelte";
  import { pop, push } from "svelte-spa-router";
  import DarkMode from "./DarkMode.svelte";
  import UserWidget from "@app/Layout/NavBar/UserWidget.svelte";
  // import { openConsole } from "@app/Overlays/consoleStore";
  import CarIcon from "@shared/PhippsyTech/svelte-ui/icons/Car.svelte";
  import Role from "@shared/Role.svelte";

  let isSmallScreen = window.innerWidth < 768;

  function handleResize() {
    isSmallScreen = window.innerWidth < 768;
  }

  onMount(async () => {
    window.addEventListener("resize", handleResize);
  });
</script>

{#if isSmallScreen}
  <div class="h-16 block"></div>

  <nav
    class="bg-gray-800 fixed bottom-0 z-10 w-full block lg:hidden text-gray-400"
  >
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between px-5">
        <svg
          on:click={() => push("/")}
          class="h-8 w-8 text-white"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
          aria-hidden="true"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
          ></path>
        </svg>

        <div class="text-gray-400 relative flex-shrink-0">
          <a href="/#/trips"><CarIcon /></a>
        </div>

        <DarkMode />

        <UserWidget />

        <!-- <Role roles={["sysadmin"]}>
          <svg
            on:click={openConsole}
            class="h-8 w-8 text-white"
            data-slot="icon"
            fill="none"
            stroke-width="1.5"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z"
            ></path>
          </svg>
        </Role> -->
        <svg
          on:click={() => pop()}
          class="h-8 w-8 text-white"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
          aria-hidden="true"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15.75 19.5L8.25 12l7.5-7.5"
          ></path>
        </svg>
      </div>
    </div>
  </nav>
{/if}
