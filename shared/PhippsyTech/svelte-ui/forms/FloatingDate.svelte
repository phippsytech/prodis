<script>
    // import {formatDate} from "@shared/utilities.js"
    export let label = "Date";
    export let value;
    export let readOnly = false;
    // export let min;
    // export let max;

    const inputId =
        "floating-date-input-" + Math.random().toString(36).substring(7);

    function formatDate(
        str_date,
        options = {
            day: "numeric",
            month: "short", // short, numeric
            year: "numeric",
        },
    ) {
        if (options.year == null) delete options.year;

        if (!str_date) return null;
        let parts = str_date.split("-");
        let year = parts[0];
        let month = parts[1];
        let day = parts[2];
        let date = new Date(
            parseInt(year, 10),
            parseInt(month, 10) - 1,
            parseInt(day, 10),
        );
        return date.toLocaleDateString("en-UK", options);
    }
</script>

{#if readOnly}
    {#if value}
        <div class="bg-white rounded-sm px-4 py-2 mb-2">
            <div class="text-xs opacity-50">{label}</div>
            {formatDate(value)}
        </div>
    {/if}
{:else}
    <div
        class="{$$restProps.class || ''} rounded-md px-3 pb-1 pt-2.5 shadow-sm ring-1 ring-inset ring-indigo-100 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
    >
    <div class="label-container flex justify-between items-center">
        <label for={inputId} class="block text-xs text-gray-900/50">{label}</label>
        {#if value}
            <div class="text-xs cursor-pointer" on:click={() => value = null}>clear</div>
        {/if}
    </div>
        <input
            id={inputId}
            type="date"
            class="block w-full border-0 p-0 text-gray-900 placeholder:text-gray-400 sm:text-sm sm:leading-6 outline-none invalid"
            bind:value
            inputmode="numeric"
            autocomplete="off"
            placeholder="dd/mm/yyyy"
            {...$$props}
        />
    </div>
{/if}