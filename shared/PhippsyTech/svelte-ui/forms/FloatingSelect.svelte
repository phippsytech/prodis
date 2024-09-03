<script>
    import { onMount } from "svelte";
    import { makeUniqueId } from "../js/base.js";

    export let label = "Select";
    export let value;
    export let valid = null;
    export let hideValidation = false;
    // export let option= null
    export let options = [];
    export let instruction = "Select an option";
    export let readOnly = false;

    let validation_error;

    if (value == null) value = instruction;

    let display_value = "";

    let id;
    let me = false; // me is used to identify if this component is being called directly
    if (!id) {
        id = makeUniqueId();
        me = true;
    }

    $: {
        if (readOnly) {
            options.forEach((item) => {
                if (item.value == value) display_value = item.option;
            });
        }
    }
</script>

{#if readOnly}
    <div class="bg-white rounded-sm px-4 py-2 mb-2">
        <div class="text-xs opacity-50">{label}</div>
        {display_value}
    </div>
{:else}
    <div
        class="rounded-md px-3 pb-1.5 pt-2.5 shadow-sm ring-1 ring-inset ring-indigo-100 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
    >
        <label for={id} class="block text-xs text-gray-900/50">{label}</label>
        <select
            {id}
            on:change
            bind:value
            class="
            block
            w-full
            px-0
            py-1
            -mx-1
            text-base
            font-normal
            text-gray-700
            bg-white bg-clip-padding
            block w-full border-0 p-0 text-gray-900 placeholder:text-gray-400 outline-none {hideValidation ==
                false && valid == true
                ? 'is-valid'
                : ''} {hideValidation == false && valid == false
                ? 'is-invalid'
                : ''} "
            required
        >
            <option selected disabled>{instruction}</option>
            {#each options as option}
                <option
                    value={option.value}
                    disabled={option.disabled}
                    selected={option.selected}>{option.option}</option
                >
            {/each}
        </select>
    </div>
{/if}
