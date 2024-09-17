<script>
    export let label = "Date & Time";
    export let value;
    export let readOnly = false;

    const inputId =
        "floating-datetime-input-" + Math.random().toString(36).substring(7);

    function formatDateTime(
        str_date,
        options = {
            day: "numeric",
            month: "short", // short, numeric
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        },
    ) {
        if (options.year == null) delete options.year;

        if (!str_date) return null;
        let [datePart, timePart] = str_date.split("T");
        let [year, month, day] = datePart.split("-");
        let date = new Date(
            parseInt(year, 10),
            parseInt(month, 10) - 1,
            parseInt(day, 10),
        );

        if (timePart) {
            let [hour, minute] = timePart.split(":");
            date.setHours(parseInt(hour, 10), parseInt(minute, 10));
        }

        return date.toLocaleDateString("en-UK", options);
    }
</script>

{#if readOnly}
    {#if value}
        <div class="bg-white rounded-sm px-4 py-2 mb-2">
            <div class="text-xs opacity-50">{label}</div>
            {formatDateTime(value)}
        </div>
    {/if}
{:else}
    <div
        class="rounded-md px-3 pb-1 pt-2.5 shadow-sm ring-1 ring-inset ring-indigo-100 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
    >
        <div class="flex justify-between gap-2">
            <label for={inputId} class="block text-xs text-gray-900/50"
                >{label}</label
            >
            {#if value}
            <div class="text-xs cursor-pointer" on:click={() => value = null}>clear</div>
            {/if}
        </div>
        <input
            id={inputId}
            type="datetime-local"
            class="block w-full border-0 p-0 text-gray-900 placeholder:text-gray-400 sm:text-sm sm:leading-6 outline-none invalid"
            bind:value
            inputmode="numeric"
            autocomplete="off"
            placeholder="dd/mm/yyyy hh:mm"
            {...$$props}
        />
    </div>
{/if}
