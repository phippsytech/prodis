<script>
    export let value = 0; // This is the total duration in minutes
    export let label = "Duration";
    export let readOnly = false;

    // No need to initialize here since $: block will set them initially
    let internalHour, internalMinute;

    // Create hour and minute options once, not reactively, as they do not change
    const hours = Array.from({ length: 24 }, (_, i) =>
        i.toString().padStart(2, "0"),
    );
    const minutes = Array.from({ length: 60 }, (_, i) =>
        i.toString().padStart(2, "0"),
    );

    // Ensure that value is always a non-negative integer
    $: {
        let parsedValue = parseInt(value);
        if (!Number.isInteger(parsedValue) || parsedValue < 0) {
            value = 0;
        } else {
            value = parsedValue;
        }
    }

    // Reactive assignments for internalHour and internalMinute
    $: internalHour = Math.floor(value / 60)
        .toString()
        .padStart(2, "0");
    $: internalMinute = (value % 60).toString().padStart(2, "0");

    function handleDurationChange() {
        // Convert hours and minutes to a single duration in minutes
        value = parseInt(internalHour) * 60 + parseInt(internalMinute);
    }
</script>

{#if readOnly}
    <div class="bg-white rounded-sm px-4 py-2 mb-2">
        <div class="text-xs opacity-50">{label}:</div>
        {internalHour}h {internalMinute}m
    </div>
{:else}
    <div
        class="rounded-md px-3 pb-1.5 pt-2.5 shadow-sm ring-1 ring-inset ring-indigo-100 bg-white mb-2"
    >
        <h3 class="text-xs text-slate-500 bg-white mb-2">{label}</h3>
        <div class="flex justify-start gap-x-2">
            <div
                class="w-auto rounded-md px-3 py-2 shadow-sm ring-1 ring-indigo-100 text-slate-700 outline-none mb-2"
            >
                <label for="hours" class="block text-xs text-slate-500 bg-white"
                    >Hours</label
                >
                <select
                    id="hours"
                    bind:value={internalHour}
                    on:change={handleDurationChange}
                    class="w-16 rounded-md px-3 py-0 bg-white text-slate-700 outline-none"
                    required
                >
                    {#each hours as hour}
                        <option value={hour}>{hour}</option>
                    {/each}
                </select>
            </div>

            <div
                class="w-auto rounded-md px-3 py-2 shadow-sm ring-1 ring-indigo-100 bg-white text-slate-700 outline-none mb-2"
            >
                <label
                    for="minutes"
                    class="block text-xs text-slate-500 bg-white">Minutes</label
                >
                <select
                    id="minutes"
                    bind:value={internalMinute}
                    on:change={handleDurationChange}
                    class="w-16 rounded-md px-3 py-0 bg-white text-slate-700 outline-none"
                    required
                >
                    {#each minutes as minute}
                        <option value={minute}>{minute}</option>
                    {/each}
                </select>
            </div>
        </div>
    </div>
{/if}
