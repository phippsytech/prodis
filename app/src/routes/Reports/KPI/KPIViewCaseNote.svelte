<script>
    import { slide } from "svelte/transition";
    import { quintOut } from "svelte/easing";
    import { jspa } from "@shared/jspa.js";

    export let line_item;

    let casenotes;

    jspa("/TimeTracking", "getTimeTracking", { id: line_item.id }).then(
        (result) => {
            casenotes = result.result.notes;
        },
    );
</script>

<div
    transition:slide={{ duration: 250, easing: quintOut }}
    class="rounded bg-white p-4 block text-sm"
>
    {#if casenotes}
        {@html casenotes.replace(/\n/g, "<br/>")}
    {:else}
        <div class="text-gray-400">No casenote</div>
    {/if}
</div>
