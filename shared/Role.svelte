<script>
    import { haveCommonElements } from "./utilities.js";
    import { RolesStore } from "./stores.js";

    export let roles;
    export let conditional = false;
</script>

{#if conditional}
    {#if haveCommonElements($RolesStore, roles)}
        <slot name="authorised" />
    {:else}
        <slot name="declined" />
    {/if}
{:else if haveCommonElements($RolesStore, roles)}
    <slot />
{:else}
    <!-- -->
{/if}
