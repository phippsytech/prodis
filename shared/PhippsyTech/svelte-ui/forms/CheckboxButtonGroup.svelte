<script>
    // NOTE: Get copilot to "suggest any improvements to CheckboxButtonGroup.svelte"
    // It seems to create more concise code, but I think it needs time to vet before
    // replacing the code below.

    import { makeUniqueId } from "../js/base.js";

    export let values = [];
    export let options = [];
    export let instructions = null;
    export let readOnly = false;

    let stored_values = Array.isArray(values) ? [...values] : [];

    const id = makeUniqueId();

    if (Array.isArray(options)) {
        options.forEach((option) => {
            option.checked = values.some((item) => item == option.value);
        });
    }

    function getCheckedValues() {
        values = options
            .filter((option) => option.checked)
            .map((option) => option.value);
    }

    $: {
        values = Array.isArray(values) ? [...values] : [];
        // this code is to ensure that the values are updated when the options are updated
        if (JSON.stringify(values) !== JSON.stringify(stored_values)) {
            if (Array.isArray(options)) {
                options.forEach((option) => {
                    // option.checked = values.some((item) => item == option.value);
                    option.checked = (Array.isArray(values) ? values : []).some(
                        (item) => item == option.value,
                    );
                });
            }
            options = options;
            stored_values = Array.isArray(values) ? [...values] : [];
        }
    }
</script>

{#if readOnly}
    <div class="bg-white rounded-sm px-4 py-2 mb-2">
        {#if values.length > 0}
            {#each options as option, index}
                {#if option.checked}<span class="px-4">{option.option}</span
                    >{/if}
            {/each}
        {:else}
            Not supplied
        {/if}
    </div>
{:else}
    {#if instructions}<p class="text-xs mb-2">{instructions}</p>{/if}
    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
        {#each options as option, index}
            <label
                for={id + index}
                class="flex items-center justify-center rounded-md py-3 px-3 text-sm font-semibold sm:flex-1 cursor-pointer focus:outline-none
                {option.checked
                    ? 'ring-2 ring-indigo-600 ring-offset-2 bg-indigo-600 text-white hover:bg-indigo-500'
                    : 'ring-1 ring-gray-200 bg-white hover:text-indigo-600 hover:ring-indigo-600 hover:bg-gray-50'}
                "
            >
                <input
                    bind:checked={option.checked}
                    id={id + index}
                    on:change={() => getCheckedValues()}
                    type="checkbox"
                    class="sr-only"
                    aria-labelledby={option.option}
                />
                <span>{option.option}</span>
            </label>
        {/each}
    </div>
{/if}
