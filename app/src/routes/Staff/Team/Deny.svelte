<script>
    import { onMount } from "svelte";
    import { XMarkIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import ParticipantSelector from "./ParticipantSelector.svelte";

    import { jspa } from "@shared/jspa.js";

    export let user_id;

    let selectedValue;
    let participants = [];

    function selectParticipant(event) {
        let newParticipant = {};
        newParticipant.name = event.detail.label;
        newParticipant.id = event.detail.value;

        jspa("/User/Participant", "denyUserParticipant", {
            user_id: user_id,
            participant_id: newParticipant.id,
        }).then((result) => {
            // Check if the participant is already in the list
            if (
                !participants.some(
                    (participant) => participant.id === newParticipant.id,
                )
            ) {
                participants = [...participants, newParticipant];
            }
        });

        selectedValue = null; // clears the select ready for the next selection
    }

    function removeParticipant(participant_id) {
        jspa("/User/Participant", "deleteUserParticipant", {
            user_id: user_id,
            participant_id: participant_id,
            allowed: 0,
        }).then((result) => {
            participants = participants.filter(
                (participant) => participant.id !== participant_id,
            );
        });
    }

    onMount(async () => {
        jspa("/User/Participant", "listDeniedUserParticipantByUserId", {
            user_id: user_id,
        }).then((result) => {
            participants = result.result;
        });
    });
</script>

<div class="bg-white px-3 pt-2 pb-4 mb-2 border border-gray-300 rounded-md">
    <h1 class="text-black text-1xl font-bold mt-0">
        Deny access to the following participants
    </h1>
    <div class="text-sm mb-2 text-gray-500">
        <!-- Denying access to a participant overrides any access granted to this user. -->
        Adding a participant to this list overrides any access previously granted
        to this user.
    </div>

    <div class="flex justify-between items-center gap-x-1 mb-2">
        <div class="flex-grow">
            <ParticipantSelector
                bind:selectedValue
                on:change={selectParticipant}
            />
        </div>
    </div>

    {#if participants.length > 0}
        <ul
            class="bg-white rounded-lg border border-gray-200 w-full text-gray-900"
        >
            {#each participants as participant, index (index)}
                <li
                    in:slide={{ duration: 200 }}
                    out:slide={{ duration: 200 }}
                    class="flex justify-between hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-gray-600 transition duration-500 {participants.length -
                        1 ==
                    index
                        ? 'rounded-b-lg'
                        : ''}border-b border-gray-200 w-full {participant.archived ==
                    1
                        ? 'text-gray-400 cursor-default'
                        : ''}"
                >
                    <div>{participant.name}</div>
                    <div
                        class="hover:text-indigo-600 cursor-pointer"
                        on:click={() => removeParticipant(participant.id)}
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </div>
                </li>
            {/each}
        </ul>
    {/if}
</div>
