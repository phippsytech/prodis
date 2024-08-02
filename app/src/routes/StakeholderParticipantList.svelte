<script>
    import Container from "@shared/Container.svelte";
    import ParticipantCard from "./ParticipantCard.svelte";
    import { onMount } from "svelte";
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { flip } from "svelte/animate";
    import { jspa } from "@shared/jspa.js";

    let clients = [];
    let client_list = [];
    let search = "";

    let showArchived = false;

    onMount(() => {
        jspa("/Client/Stakeholder", "listStakeholderClientsByUserId", {}).then(
            (result) => {
                clients = result.result;

                clients.sort(function (a, b) {
                    const nameA = a.name.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.name.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });
            },
        );
    });

    $: {
        // filter by client name
        // there is a problem which will mean if client.name is removed it will effectively hide the record
        client_list = clients.filter(
            (client) =>
                client.name &&
                client.name.toLowerCase().includes(search.toLowerCase()) ==
                    true,
        );
        client_list = client_list.filter((client) => client.archived != 1);
        // filter by client name
        // there is a problem which will mean if client.name is removed it will effectively hide the record
        // clientList = clients.filter(client=>(client.name && client.name.toLowerCase().includes(search.toLowerCase())==true));
        // if (!showArchived) clientList = clientList.filter(client=>client.archived != 1);
    }
</script>

<Container>
    <div class="font-semibold mb-2 text-sm">Stakeholder Participants</div>
    {#if clients.length > 9}
        <div class="bg-white px-4 py-4 mb-2">
            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
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
                        placeholder="Filter by participant name ..."
                        type="search"
                        name="search"
                    />
                </div>
            </div>
        </div>
    {/if}

    <div
        class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
    >
        {#each client_list as client, index (client.id)}
            <div
                animate:flip={{ duration: 350 }}
                in:slide|global={{ duration: 50, delay: 10 * index }}
            >
                <ParticipantCard {client} />
            </div>
        {/each}
    </div>
</Container>
