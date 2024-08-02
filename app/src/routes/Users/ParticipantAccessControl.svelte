<script>
    import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
    import AllowParticipantList from "./ParticipantAccessControl/Allow.svelte";
    import DenyParticipantList from "./ParticipantAccessControl/Deny.svelte";

    export let user = {};
</script>

{#if user.id}
    <div
        class="bg-white px-3 pt-2 pb-4 mb-2 border border-indigo-100 rounded-md"
    >
        <div class="text-xs opacity-50 mb-2">Participant Access Control</div>

        <div class="mb-2">
            Participants are accessible to assigned staff and their supervisors.
        </div>

        <div
            class="bg-white px-3 pt-2 pb-2 mb-2 border border-indigo-100 rounded-md"
        >
            <Toggle
                bind:value={user.access_all_participants}
                label_on="Access all participants"
                label_off="Access all participants"
            />
        </div>

        {#if !user.access_all_participants}
            <AllowParticipantList user_id={user.id} />
        {/if}

        <DenyParticipantList user_id={user.id} />
    </div>
{/if}
