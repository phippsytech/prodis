<script>
    import { onMount } from "svelte";
    import { push } from "svelte-spa-router";
    import { PlusIcon, XMarkIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import Role from "@shared/Role.svelte";
    import { jspa } from "@shared/jspa.js";

    export let client = {};

    let stakeholders = [];

    onMount(() => {
        jspa("/Client/Stakeholder", "listStakeholdersByClientId", {
            client_id: client.id,
        })
            .then((result) => {
                stakeholders = result.result;
            })
            .catch((error) => {});
    });

    function removeStakeholder(stakeholder_id) {
        jspa("/Client/Stakeholder", "deleteStakeholder", {
            id: stakeholder_id,
        }).then((result) => {
            stakeholders = stakeholders.filter(
                (stakeholder) => stakeholder.id !== stakeholder_id,
            );
        });
    }
</script>

<div class="flex justify-between">
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
        Stakeholders
    </h1>
    <Role roles={["admin"]}>
        <div
            on:click={() => push("/clients/" + client.id + "/stakeholders/add")}
        >
            <PlusIcon class="w-6 h-6" />
        </div>
    </Role>
</div>

<ul
    class="bg-white rounded-lg border border-gray-200 w-full text-gray-900 mb-2"
>
    {#if !stakeholders || stakeholders.length == 0}
        <li
            class=" px-3 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default text-sm"
        >
            No stakeholders have been allocated to this client
        </li>
    {:else}
        {#each stakeholders as stakeholder, index (stakeholder.id)}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                class="px-3 py-2 border-b border-gray-200 w-full rounded-t-lg"
            >
                <div class="flex justify-between">
                    <div
                        on:click={() =>
                            push(
                                "/clients/" +
                                    client.id +
                                    "/stakeholders/" +
                                    stakeholder.id,
                            )}
                        class=""
                    >
                        {stakeholder.name}
                        {#if stakeholder.representative == 1}
                            <span
                                class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-blue-600 text-white rounded-full"
                                >Representative
                            </span>
                        {/if}<br />
                        {#if stakeholder.relationship}<div class="text-xs">
                                {stakeholder.relationship}
                            </div>{/if}
                        {#if stakeholder.email}<div class="text-xs">
                                {stakeholder.email}
                            </div>{/if}
                        {#if stakeholder.phone}<div class="text-xs">
                                {stakeholder.phone}
                            </div>{/if}
                        {#if stakeholder.notes}<div class="text-xs opacity-50">
                                {stakeholder.notes}
                            </div>{/if}
                    </div>

                    <Role roles={["admin"]}>
                        <div
                            on:click={() => removeStakeholder(stakeholder.id)}
                            class="cursor-pointer"
                        >
                            <XMarkIcon class="w-4 h-4 inline" />
                        </div>
                    </Role>
                </div>
            </li>
        {/each}
    {/if}
</ul>
