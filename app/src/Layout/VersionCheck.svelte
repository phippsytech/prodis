<script>
  import { get } from "svelte/store";
  import { API_URL } from "@shared/stores.js";
  import { slide } from "svelte/transition";
  import { VersionStore } from "@shared/stores.js";

  $: versionStore = $VersionStore;
  /**
 TODO:
 Instead of polling the server for versions, we can use a service worker to check for updates.

Actually - I was going to use a websocket, but code pilot suggested service worker,
 so maybe I neeed to look into this?

 */

  const api_url = get(API_URL);
  const versionMetaTag = document.querySelector('meta[name="version"]');

  let new_version_available = false;
  let version = "Development";

  if (versionMetaTag) version = versionMetaTag.getAttribute("content");

  function checkVersion() {
    // conditions to skip checking for a new version
    if (version == "Development") return; // don't check if we are in development
    if (new_version_available) return; // we already know there is a new version

    // check the version on the server
    fetch(api_url + "/App", {
      method: "GET",
      headers: { "Content-Type": "application/json; charset=utf-8" },
      mode: "cors",
    })
      .then(async (response) => {
        response.json().then((data) => {
          if (data.version != version) {
            new_version_available = true;
          }
        });
      })
      .catch((error) => {});
  }

  function reload() {
    window.location.reload();
  }

  $: if (versionStore.updated) {
    new_version_available = true;
    VersionStore.set({ updated: false });
  }

  // Version Check
  checkVersion(); // check on load
  // setInterval(checkVersion, 30000); // check every minute
</script>

{#if new_version_available}
  <div
    class="border-l-4 border-yellow-400 bg-yellow-50 p-4"
    transition:slide={{ x: -100 }}
  >
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
        <p class="text-sm text-yellow-700">
          New version available. &nbsp;
          <span
            on:click={() => reload()}
            class="font-medium text-yellow-700 underline hover:text-yellow-600 cursor-pointer"
            >Click here to update.</span
          >
        </p>
      </div>
    </div>
  </div>
{/if}
