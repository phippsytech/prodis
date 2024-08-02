<script>
    import { push } from "svelte-spa-router";
    import { onMount } from "svelte";
    import { BreadcrumbStore } from "@shared/stores.js";

    import { jspa } from "@shared/jspa.js";
    import { slide } from "svelte/transition";
    import { flip } from "svelte/animate";
    import CredentialCard from "./CredentialCard.svelte";

    let credentials = [];

    onMount(async () => {
        jspa("/Credential", "listCredentials", {}).then((result) => {
            credentials = result.result;

            credentials.sort(function (a, b) {
                const nameA = a.name.toUpperCase(); // ignore upper and lowercase
                const nameB = b.name.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });
        });

        BreadcrumbStore.set({
            path: [{ url: null, name: "Credentials" }],
        });
    });
</script>

<div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Credentials
        </div>
        <p class=" text-sm text-gray-700">
            This is the list of credentials that you collect from staff members.
        </p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/credentials/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Add credential</button
        >
    </div>
</div>

{#if credentials}
    <div
        class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
    >
        {#each credentials as credential, index (credential.id)}
            <div
                class="cursor-pointer"
                animate:flip={{ duration: 350 }}
                in:slide|global={{ duration: 50, delay: 10 * index }}
                out:slide|global={{ duration: 50 }}
            >
                <CredentialCard
                    on:click={() => push("/credentials/" + credential.id)}
                    {credential}
                />
            </div>
        {/each}
    </div>
{/if}
