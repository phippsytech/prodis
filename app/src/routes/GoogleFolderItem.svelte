<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";

    export let folders = [];
    export let selected_folder_id;
    export let parent_folder_id = null;
    export let drive_id = null;

    let height = 450;

    function select(folder_id) {
        selected_folder_id = folder_id;
    }

    function toggle(folder_id) {
        let folder = folders.find((folder) => folder.id == folder_id);
        folder.expanded = !folder.expanded;
        folders = folders;
    }

    let loading = true;

    onMount(() => {
        jspa("/Google", "listFolders", {
            drive_id: drive_id,
            folder_id: parent_folder_id,
        })
            .then((result) => {
                folders = result.result;
            })
            .finally(() => {
                loading = false;
            });
    });
</script>

<div
    class="min-h-400 overflow-y-auto rounded-lg border border-gray-200 p-2 outline-none bg-white"
    style={`max-height: ${height}px; min-height: ${height}px;`}
>
    <ul class="block ml-4 pl-2">
        {#if loading}
            <li class="flex items-center">
                <div class="flex px-2 py-1 text-gray-600 cursor-pointer">
                    <div class="flex items-center">
                        <svg
                            class="animate-spin flex-shrink-0 h-5 w-5 text-indigo-600"
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
                        <span class="pl-2">loading</span>
                    </div>
                </div>
            </li>
        {:else}
            {#each folders as folder}
                <li class="flex items-center">
                    <div
                        class="flex px-2 py-1 {folder.id == selected_folder_id
                            ? 'bg-indigo-600 text-white font-bold'
                            : 'text-gray-600 cursor-pointer'}"
                    >
                        <div class="flex items-center">
                            {#if folder.expanded}
                                <svg
                                    on:click={() => toggle(folder.id)}
                                    class="flex-shrink-0 w-5 h-5 inline text-orange-400 hover:text-indigo-600"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                    aria-hidden="true"
                                    style="filter: drop-shadow(1px 2px 2px rgba(0, 0, 0, 0.25));"
                                >
                                    <path
                                        d="M4.75 3A1.75 1.75 0 003 4.75v2.752l.104-.002h13.792c.035 0 .07 0 .104.002V6.75A1.75 1.75 0 0015.25 5h-3.836a.25.25 0 01-.177-.073L9.823 3.513A1.75 1.75 0 008.586 3H4.75zM3.104 9a1.75 1.75 0 00-1.673 2.265l1.385 4.5A1.75 1.75 0 004.488 17h11.023a1.75 1.75 0 001.673-1.235l1.384-4.5A1.75 1.75 0 0016.896 9H3.104z"
                                    ></path>
                                </svg>
                            {:else}
                                <svg
                                    on:click={() => toggle(folder.id)}
                                    class="w-5 h-5 inline text-orange-400 hover:text-indigo-600"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M3.75 3A1.75 1.75 0 002 4.75v3.26a3.235 3.235 0 011.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0016.25 5h-4.836a.25.25 0 01-.177-.073L9.823 3.513A1.75 1.75 0 008.586 3H3.75zM3.75 9A1.75 1.75 0 002 10.75v4.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0018 15.25v-4.5A1.75 1.75 0 0016.25 9H3.75z"
                                    ></path>
                                </svg>
                            {/if}
                            <span
                                on:click={() => select(folder.id)}
                                class="pl-2 {folder.id == selected_folder_id
                                    ? ''
                                    : 'hover:text-indigo-600'} "
                            >
                                {folder.name}
                            </span>
                        </div>
                        <svg
                            class="ml-2 w-5 h-5 text-white {folder.id ==
                            selected_folder_id
                                ? ''
                                : 'hidden'}"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                            ></path>
                        </svg>
                    </div>
                </li>
                {#if folder.expanded}
                    <svelte:self
                        bind:selected_folder_id
                        parent_folder_id={folder.id}
                    />
                {/if}
            {:else}
                <span class="text-xs opacity-50">No folders</span>
            {/each}
        {/if}
    </ul>
</div>
