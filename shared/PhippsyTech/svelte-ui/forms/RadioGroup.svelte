<script>
    /*
    Check out https://formvalidation.io/ to see the way radio fields look when they are vaildated.
    Think the trick is mark them validated when an option is selected.RadioGroup
    The only time they could be considered invalid is when the form is submitted and an option has not been selected.
*/

    import { makeUniqueId } from "../js/base.js";
    export let value = null;
    export let options = [];

    const id = makeUniqueId();
</script>

<div class="my-2 mb-0 space-y-2">
    {#each options as option, index}
        <div
            class="flex items-center gap-x-3 hover:bg-indigo-50 rounded-md cursor-pointer"
        >
            <input
                bind:group={value}
                type="radio"
                class="h-4 w-4 border-indigo-100 text-indigo-600 focus:ring-indigo-600 cursor-pointer"
                value={option.value}
                id={id + index}
                on:change
                on:click
            />
            <label
                class="block text-sm font-medium leading-6 text-gray-900 cursor-pointer"
                for={id + index}
            >
                {option.option}
            </label>
        </div>
    {:else}
        <!-- ERR: No options provided -->
    {/each}
</div>
