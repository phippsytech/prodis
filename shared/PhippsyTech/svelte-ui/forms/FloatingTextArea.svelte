<script>
    import { makeUniqueId } from "../js/base.js";
    export let value = null;
    export let label = null;
    export let placeholder = "";
    export let readOnly = false;

    let id;
    let me = false; // me is used to identify if this component is being called directly
    if (!id) {
        id = makeUniqueId();
        me = true;
    }

    $: if (value == null) value = null;
</script>

{#if readOnly}
    {#if value}
        <div class="bg-white rounded-sm px-4 py-2 mb-2">
            <div class="text-xs opacity-50">{label}</div>
            {value}
        </div>
    {/if}
{:else}
    <div
        class="rounded-md px-3 pb-1.5 pt-2.5 shadow-sm ring-1 ring-inset ring-indigo-100 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
    >
        {#if label}<label for={id} class="block text-xs text-gray-900/50"
                >{label}</label
            >{/if}
        <textarea
            {id}
            {placeholder}
            rows="4"
            bind:value
            {...$$props}
            class="block w-full resize-none border-0 bg-transparent text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6 outline-none"
        ></textarea>
    </div>
{/if}
