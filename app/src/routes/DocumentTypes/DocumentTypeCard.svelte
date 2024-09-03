<script>
    import { push } from "svelte-spa-router";
    export let documentType = {};
</script>

<div
    on:click
    class="relative flex flex-col h-full space-x-3 rounded-lg border border-gray-300 bg-white px-4 py-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:bg-indigo-600 hover:text-white group"
>
    <div class="min-w-0 flex-1">
        <span class="focus:outline-none">
            <span class="absolute inset-0" aria-hidden="true"></span>
            <div
                class="text-sm font-medium text-gray-900 group-hover:text-white flex justify-between"
            >
                <span class="font-bold text-gray-800">{documentType.name}</span>
                {#if documentType.is_required}
                    <span class="text-xs uppercase text-slate-300"
                        >REQUIRED</span
                    >
                {/if}
            </div>
            {#if documentType.description}
                <div class="text-xs text-slate-600">
                    {documentType.description}
                </div>
            {/if}
        </span>
    </div>
</div>
