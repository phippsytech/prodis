<script>
    import { PlusIcon } from "heroicons-svelte/24/outline";

    import Stakeholder from "./Stakeholder.svelte";
    import { slide } from "svelte/transition";
    import { ModalStore } from "@app/Overlays/stores";
    import { jspa } from "@shared/jspa.js";

    import { onMount } from "svelte";

    export let client = {};
    export let client_id;
    let stakeholder = {};
    let stakeholders = [];

    onMount(() => {
        jspa("/Client/Stakeholder", "listStakeholdersByClientId", {
            client_id: client_id,
        })
            .then((result) => {
                stakeholders = result.result;
            })
            .catch((error) => {});
    });

    function addStakeholder() {
        stakeholder.client_id = client_id;
        ModalStore.set({
            label: "Add Stakeholder",
            show: true,
            props: stakeholder,
            component: Stakeholder,
            action_label: "Add",
            action: () => createStakeholder(),
        });
    }

    function editStakeholder(stakeholder) {
        // stakeholder.client_id = client_id
        ModalStore.set({
            label: "Update Stakeholder",
            show: true,
            props: stakeholder,
            component: Stakeholder,
            action_label: "Update",
            action: () => updateStakeholder(stakeholder),
            delete: () => deleteStakeholder(stakeholder.id),
        });
    }

    function deleteStakeholder(stakeholder_id) {
        jspa("/Client/Stakeholder", "deleteStakeholder", {
            id: stakeholder_id,
        }).then((result) => {
            stakeholders = stakeholders.filter(
                (stakeholder) => stakeholder.id != stakeholder_id,
            );
        });
    }

    function createStakeholder() {
        jspa("/Client/Stakeholder", "addStakeholder", stakeholder).then(
            (result) => {
                stakeholders.push(result.result);
                stakeholders = stakeholders;
            },
        );
    }

    function updateStakeholder(stakeholder) {
        jspa("/Client/Stakeholder", "updateStakeholder", stakeholder).then(
            (result) => {
                stakeholders = stakeholders.map((stakeholder) => {
                    if (stakeholder.id == stakeholder.id) {
                        stakeholder = stakeholder;
                    }
                    return stakeholder;
                });
            },
        );
    }
</script>

<div class="flex justify-between">
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
        Stakeholders
    </h1>
    <div on:click={() => addStakeholder()}><PlusIcon class="w-6 h-6" /></div>
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
        {#each stakeholders as item, index (item.id)}
            <li
                on:click={() => editStakeholder(item)}
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                class="px-3 py-2 border-b border-gray-200 w-full rounded-t-lg cursor-pointer"
            >
                <div class="flex justify-between">
                    <div class="">
                        {item.name}
                        {#if item.representative == 1}
                            <span
                                class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-indigo-600 text-white rounded-full"
                                >Representative
                            </span>
                        {/if}<br />
                        {#if item.relationship}<div class="text-xs">
                                {item.relationship}
                            </div>{/if}
                        {#if item.email}<div class="text-xs">
                                {item.email}
                            </div>{/if}
                        {#if item.phone}<div class="text-xs">
                                {item.phone}
                            </div>{/if}
                        {#if item.notes}<div class="text-xs opacity-50">
                                {item.notes}
                            </div>{/if}
                    </div>
                </div>
            </li>
        {/each}
    {/if}
</ul>
