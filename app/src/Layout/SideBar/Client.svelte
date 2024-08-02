<script>
    import List from "../../routes/Clients/List.svelte";
    import ClientNavList from "./NavList/ClientNavList.svelte";
    import { jspa } from "@shared/jspa.js";

    const pathname = window.location.hash;
    const segments = pathname.split("/");
    const client_id = segments[2];

    let client = {};

    // $: {
    if (client_id) {
        jspa("/Client", "getClient", { id: client_id }).then((result) => {
            client = result.result;
            if (client.sil_enabled == "1") client.sil_enabled = true;
            if (client.sil_enabled == "0") client.sil_enabled = false;
        });
    } else {
        client = {};
    }
    // }

    let search = "";
</script>

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
                    <div class="flex justify-between items-center px-5">
                        <h2
                            class="mt-2 text-md font-bold leading-7 text-gray-900 truncate tracking-tight mt-0 mb-2"
                        >
                            {client.name}
                        </h2>
                        <a href="/#/clients/{client.id}/settings">
                            <svg
                                class="h-5 w-5 text-gray-700 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
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
                                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                ></path>
                            </svg></a
                        >
                    </div>

                    <ClientNavList bind:client />
                </div>
            </li>
        </ul>
    {/if}
</div>
