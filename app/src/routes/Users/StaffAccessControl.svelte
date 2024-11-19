<script>
    import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
    import AllowStaffList from "./StaffAccessControl/Allow.svelte";
    // import DenyStaffList from "./StaffAccessControl/Deny.svelte";

    export let user = {};
</script>

{#if user.id}
    <div
        class="bg-white px-3 pt-2 pb-4 mb-2 border border-indigo-100 rounded-md"
    >
        <div class="text-xs opacity-50 mb-2">Staff Access Control</div>

        <div class="mb-2">
            Staffs are accessible to assigned staff and their supervisors.
        </div>

        <div
            class="bg-white px-3 pt-2 pb-2 mb-2 border border-indigo-100 rounded-md"
        >
            <Toggle
                bind:value={user.access_all_staffs}
                label_on="Access all staffs"
                label_off="Access all staffs"
            />
        </div>

        {#if !user.access_all_staffs}
            <AllowStaffList user_id={user.id} />
        {/if}

        <!-- <DenyStaffList user_id={user.id} /> -->
    </div>
{/if}
