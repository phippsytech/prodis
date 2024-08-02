<script>
    import ExpandIndicator from './ExpandIndicator.svelte'

    export let label="Menu Item"
    export let expanded=false;
    export let children=false;
    export let selected=false;
    export let href="/#/";

</script>

<a {href} class="flex justify-between text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 px-8 text-sm {(selected)?'leading-6 font-semibold text-indigo-600 bg-gray-50 ':''}">
    <div class="flex gap-x-3 items-center">
        <slot></slot>
        {label}
    </div>
    {#if children || expanded}
    <div class="flex items-center justify-end gap-x-4 flex-none">
        <ExpandIndicator {expanded} class="h-5 w-5 flex-none text-gray-400 shrink-0" />
    </div>
    {/if}
</a>