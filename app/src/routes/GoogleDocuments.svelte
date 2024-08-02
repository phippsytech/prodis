<script>
  import { onMount } from "svelte";

  import { jspa } from "@shared/jspa.js";
  import { SpinnerStore } from "@app/Overlays/stores.js";

  export let folder_id; // = "1-tTFtKFGcaC_5meNuxxTP-FG5AiYpKtE";
  export let parent_folder_id; //
  export let drive_id = null;
  export let base_url = "/documents";

  let folders = [];
  let files = [];
  let path = [];

  let loading = true;

  onMount(() => {
    openFolder(folder_id);
  });

  function openFolder(folder_id) {
    loading = true;
    Promise.all([
      jspa("/Google", "listFolders", {
        drive_id: drive_id,
        folder_id: folder_id,
      }),
      jspa("/Google", "listFiles", {
        drive_id: drive_id,
        folder_id: folder_id,
      }),
      jspa("/Google", "getBreadcrumbs", {
        folder_id: folder_id,
        parent_folder_id: parent_folder_id,
      }),
    ])
      .then(([foldersResult, filesResult, breadcrumbsResult]) => {
        folders = foldersResult.result;
        files = filesResult.result;
        path = breadcrumbsResult.result.map((item) => {
          item.url = "/#" + base_url + "/" + item.folder_id;
          item.name = item.folder_name;
          return item;
        });
        loading = false;
      })
      .finally(() => {
        loading = false;
      });

    // replace(base_url+"/"+folder_id);
  }

  // function openFile(file_id){
  //     loading=true;
  //     jspa("/Google", "getFile", {drive_id:drive_id, file_id:file_id}).then((result)=>{
  //         let url = result.result;
  //         window.open(url, '_blank');
  //     }).finally(()=>{loading=false;});
  // }

  function openFile(file_id) {
    SpinnerStore.set({ show: true, message: "Getting File" });
    jspa("/Google", "getFile", { file_id: file_id })
      .then((result) => {
        let url = result.result;
        window.open(url, "_blank");
      })
      .finally(() => {
        SpinnerStore.set({ show: false });
      });
  }
</script>

<nav
  class="border-b border-gray-100 rounded-t-lg bg-white p-2 w-full mt-0"
  aria-label="Breadcrumb"
>
  <div class="">
    <ol role="list" class="flex items-center space-x-2">
      {#if path.length > 0}
        {#each path as item, index}
          <li>
            <div class="flex items-center">
              {#if index > 0}
                <svg
                  class="h-5 w-5 flex-shrink-0 text-gray-400"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                    clip-rule="evenodd"
                  />
                </svg>
              {/if}
              <span
                on:click={() => openFolder(item.folder_id)}
                class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700 cursor-pointer"
                >{item.name}</span
              >
            </div>
          </li>
        {/each}
      {/if}
    </ol>
  </div>
</nav>

{#if loading}
  <div
    class="bg-white flex justify-center items-center h-full text-center text-gray-400"
  >
    <svg
      class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
    Loading
  </div>
{:else}
  <!-- larger screen -->
  <div class="hidden lg:block h-full">
    <div
      class="rounded-b-lg bg-white grid grid-cols-3 gap-3 lg:grid-cols-5 xl:grid-cols-7"
    >
      {#each folders as folder, index}
        <div
          on:click={() => openFolder(folder.id)}
          class="flex group text-center items-center flex-col justify-top rounded-md py-3 px-3 text-sm sm:flex-1 cursor-pointer focus:outline-none {folder.checked
            ? ''
            : ' hover:text-indigo-600 hover:ring-indigo-600 hover:bg-gray-50'}"
        >
          <svg
            class="w-12 h-12 text-yellow-400 group-hover:text-indigo-600"
            fill="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
          >
            <path
              d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z"
            ></path>
          </svg>

          <span class="break-words overflow-wrap break-words w-full"
            >{folder.name}</span
          >
        </div>
      {/each}

      {#each files as file}
        <div
          on:click={() => openFile(file.id)}
          class="flex group text-center items-center flex-col justify-top rounded-md py-3 px-3 text-sm sm:flex-1 cursor-pointer focus:outline-none {file.checked
            ? ''
            : ' hover:text-indigo-600 hover:ring-indigo-600 hover:bg-gray-50'}"
        >
          <svg
            class="w-12 h-12 text-white group-hover:text-indigo-600"
            fill="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
            style="filter: drop-shadow(0px 0px 1px rgba(0, 0, 0, 0.5));"
          >
            <path
              d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z"
            ></path>
            <path
              d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"
            ></path>
          </svg>
          <span class="break-words overflow-wrap break-words w-full"
            >{file.name}</span
          >
        </div>
      {/each}
    </div>
  </div>

  <!-- smaller screen-->
  <!-- <div class="block lg:hidden bg-white"> -->
  <div class="block bg-white">
    <ul role="list" class="divide-y divide-gray-100 cursor-pointer">
      {#each folders as folder, index}
        <li
          class="relative flex justify-between bg-white py-1 px-5 text-gray-700 hover:bg-indigo-600 hover:text-gray-50"
        >
          <div on:click={() => openFolder(folder.id)} class="flex gap-x-2">
            <svg
              class="w-6 h-6 text-yellow-400 flex-shrink-0"
              fill="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
              aria-hidden="true"
            >
              <path
                d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z"
              ></path>
            </svg>
            <span class="group-hover:text-white">{folder.name}</span>
          </div>
        </li>
      {/each}

      {#each files as file}
        <li
          class="relative flex justify-between bg-white py-1 px-5 text-gray-700 hover:bg-indigo-600 hover:text-gray-50"
        >
          <div on:click={() => openFolder(file.id)} class="flex gap-x-2">
            <svg
              class="w-6 h-6 text-white flex-shrink-0 group-hover:text-white"
              fill="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
              aria-hidden="true"
              style="filter: drop-shadow(0px 0px 1px rgba(0, 0, 0, 0.5));"
            >
              <path
                d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z"
              ></path>
              <path
                d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"
              ></path>
            </svg>
            <span class="group-hover:text-white">{file.name}</span>
          </div>
        </li>
      {/each}
    </ul>
  </div>
{/if}
