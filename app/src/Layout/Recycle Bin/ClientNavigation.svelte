<script>
    import { onMount } from "svelte";
    import List from "../routes/Clients/List.svelte";
    import ClientNavList from "./ClientNavList.svelte";
    import { ParamsStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";

    $: params = $ParamsStore;

    let client = {};

    $: {
        if (
            params &&
            typeof params.client_id != "undefined" &&
            params.client_id != null &&
            client &&
            params.client_id != client.id
        ) {
            jspa("/Client", "getClient", { id: params.client_id }).then(
                (result) => {
                    client = result.result;
                    client = client;
                },
            );
        }
    }

    let search = "";
</script>

<div
    class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col mt-16 z-10"
>
    <div
        class="flex grow flex-col gap-y-2 overflow-y-auto border-r border-gray-200 bg-white"
    >
        <div class="flex h-16 shrink-0 items-center">
            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-2 px-6">
                <div class="relative flex flex-1">
                    <label for="search-field" class="sr-only">Search</label>
                    <svg
                        class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <input
                        bind:value={search}
                        id="search-field"
                        class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                        placeholder="search by client name..."
                        type="search"
                        name="search"
                        autofocus
                    />
                </div>

                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <button
                        type="button"
                        class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Add client</span>
                        <svg
                            class="h-6 w-6"
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
                                d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {#if search.length > 0}
            <List bind:search />
        {:else if !client.id}{:else}
            <ul role="list" class="divide-y divide-gray-100 bg-white">
                <li class="relative flex justify-between py-2 text-gray-700">
                    <div class="min-w-0 flex-auto">
                        <h2
                            class="mt-2 text-md font-bold leading-7 text-gray-900 truncate tracking-tight mt-0 mb-2 px-5"
                        >
                            {client.name}
                        </h2>
                        <ClientNavList bind:client />
                    </div>
                </li>
            </ul>
        {/if}
    </div>
</div>
