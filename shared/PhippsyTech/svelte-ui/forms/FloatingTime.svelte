<script>
    export let label = "Time";
    export let value;
    export let readOnly = false;

    const inputId =
        "floating-time-input-" + Math.random().toString(36).substring(7);
</script>

{#if readOnly}
    {#if value}
        <div class="bg-white inline-block rounded-sm px-4 py-2 mb-2">
            <div class="text-xs opacity-50">{label}</div>
            {value}
        </div>
    {:else}
        Not supplied
    {/if}
{:else}
    <div
        class="bg-white inline-block rounded-lg py-1 p-2 border border-indigo-100 mb-2"
    >
        <label for={inputId} class="text-xs opacity-50 p-0 m-0 block"
            >{label}</label
        >
        <input
            id={inputId}
            type="time"
            class="p-0 m-0 outline-none bg-white"
            bind:value
            pattern="[0-9]{2}:[0-9]{2}"
            {...$$props}
        />
    </div>
{/if}
