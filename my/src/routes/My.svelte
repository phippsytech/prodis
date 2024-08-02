<script>
    import { onMount } from "svelte";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";

    let selected_staff_id = null;

    let clients = [];

    onMount(async () => {
        let stakeholder_id = null;

        await jspa(
            "/Client/Stakeholder",
            "listStakeholderClientsByUserId",
            {},
        ).then((result) => {
            clients = result.result;
            console.log(clients);
            clients.sort(function (a, b) {
                const nameA = a.name.toUpperCase(); // ignore upper and lowercase
                const nameB = b.name.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });
        });
    });
</script>

<div class="flex justify-center p-4">
    <div class="w-full" style="max-width:400px;">
        <p class="text-gray-700 uppercase mb-0">Crystel Care</p>
        <div class="text-3xl mb-4">Participants</div>

        <ul
            class="bg-white rounded-lg border border-gray-200 w-full text-gray-900 mb-4"
        >
            {#each clients as client, index}
                <li
                    in:slide={{ duration: 200 }}
                    out:slide={{ duration: 200 }}
                    on:click={() => select(client.id)}
                    class="px-4 py-4 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {clients.length -
                        1 ==
                    index
                        ? 'rounded-b-lg'
                        : ''}border-b border-gray-200 w-full"
                >
                    <div class="font-bold">{client.name}</div>
                </li>
            {:else}
                <li
                    class="px-6 py-2 border-b border-gray-200 w-full rounded-t-lg rounded-b-lg text-gray-400 cursor-default"
                >
                    Participant list not found. <a
                        class="text-blue-400 underline"
                        href="/#/logout">Logout</a
                    >
                </li>
            {/each}
        </ul>
    </div>
</div>
