<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { slide } from "svelte/transition";
    import { getClient } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatDate, formatCurrency, timeAgo } from "@shared/utilities.js";

    export let params;

    let client_id = params.client_id;
    let activities = [];
    // let invoice_summary = [];
    let client = {};

    onMount(async () => {
        client = await getClient(client_id);
        BreadcrumbStore.set({
            path: [
                { url: "/clients", name: "Clients" },
                { url: "/clients/" + client_id, name: client.name },
            ],
        });
    });

    jspa("/ActivityLog", "listActivityLogs", { entity_type: 'participant', entity_id: client_id }).then(
        (result) => {
            activities = result.result;
        },
    );
</script>

<h2 class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular">
    Activity History
</h2>

<!-- Header -->
<div class="hidden sm:block">
    <div
        class="grid grid-cols-2 border-b border-gray-200 items-center py-1 text-xs font-medium text-gray-500"
        style="grid-template-columns: 6fr 1fr;"
    >
        <div class="grid grid-cols-3 gap-4 items-center">
            <div>Action Type</div>
            <div>Reason</div>
            <div>User</div>
        </div>
        <div>Date</div>
    </div>
</div>

<!-- Activities -->
{#if activities.length > 0}
    {#key activities}
        {#each activities as item, index}
            <div
                in:slide|global={{ duration: 150 }}
                class="grid grid-cols-2 border-b border-gray-200 items-center hover:bg-indigo-50 py-1 cursor-pointer"
                style="grid-template-columns: 6fr 1fr;"
            >
                <div
                    class="grid grid-cols-1 gap-0 sm:gap-4 sm:grid-cols-3 w-full items-center"
                >
                    <div class="text-sm">{item.action_type}</div>
                    <div class="text-xs whitespace-no-wrap">
                        {item.reason}
                    </div>
                    <div class="text-xs whitespace-no-wrap">
                        {item.user_name}
                    </div>
                </div>
                <div class="font-medium">{item.timestamp}</div>
            </div>
        {/each}
    {/key}
{:else}
    <div class="text-center text-gray-500 py-4">
        There are no records.
    </div>
{/if}
