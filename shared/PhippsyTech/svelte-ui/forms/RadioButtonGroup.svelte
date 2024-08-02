<script>
    /* check out https://formvalidation.io/ to see the way radio fields look when they are vaildated.
   
    Think the trick is mark them validated when an option is selected.RadioGroup
   
   The only time they could be considered invalid is when the form is submitted and an option has not been selected.
   */
    import { makeUniqueId } from "../js/base.js";
    export let value = null;
    export let label = null;
    export let options = [];
    export let readOnly = false;
    export let columns = 3;

    const id = makeUniqueId();

    let display_value = "";
    $: {
        if (readOnly) {
            options.forEach((item) => {
                if (item.value == value) display_value = item.option;
            });
        }
    }

    function select(selected_value) {
        value = selected_value;
    }
</script>

{#if readOnly}
    {#if display_value != ""}
        <div class="bg-white rounded-sm px-4 py-2 mb-2">
            {#if label}<div class="text-xs opacity-50">{label}</div>
            {/if}
            {display_value}
        </div>
    {:else}
        Not supplied
    {/if}
{:else}
    <div class="grid grid-cols-{columns} gap-3 sm:grid-cols-{columns + 1}">
        {#each options as option, index}
            <!-- class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"     -->
            <label
                class="flex items-center justify-center rounded-md px-3 py-2 text-sm font-semibold sm:flex-1 cursor-pointer focus:outline-none {option.value ==
                value
                    ? 'ring-2 ring-indigo-600 ring-offset-2 bg-indigo-600 text-white hover:bg-indigo-500'
                    : 'ring-1 ring-indigo-100 bg-white hover:text-indigo-600 hover:ring-indigo-600 hover:bg-gray-50'}"
            >
                <input
                    on:click={() => select(option.value)}
                    type="radio"
                    class="sr-only"
                    aria-labelledby={option.option}
                />
                <span>{option.option}</span>
            </label>
        {/each}
    </div>
{/if}
