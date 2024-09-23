<script>
    import { createEventDispatcher } from "svelte";
    import { makeUniqueId } from "../js/base.js";

    export let label = "Select";
    export let value;
    export let valid = null;
    export let hideValidation = false;
    export let options = [];
    export let instruction = "Select an option";
    export let readOnly = false;
    export let validation_error = "Please select an option";
    export let clearable = false;

    let display_value = "";

    let id;
    let me = false; // me is used to identify if this component is being called directly

    const dispatch = createEventDispatcher();

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

    let selectValue = value;

    $: {
     
        const selectedOption = options.find((option) => String(option.value) === String(value));
        
        if (!value || !selectedOption) {
            selectValue = value; // Default display when no value is selected
        } else {
            selectValue = selectedOption.option; // Display the staff name
            console.log("Selected option: ", selectValue);
        }
       
    }

    function handleChange(event) {
        if (event.target.value !== "") {
            value = event.target.value;
            validate();
        } else {
            value = null;
        }
        dispatch("change", { value });
    }

    function validate() {
        if (value === null) {
            valid = false;
        } else {
            valid = true;
        }
    }

    function handleBlur() {
        validate();
    }

    function clear() {
        value = null;
        selectValue = "";
        valid = null;
        dispatch("change", { value: null });
    }
</script>

{#if readOnly}
    <div class="bg-white rounded-sm px-4 py-2 mb-2">
        <div class="text-xs opacity-50">{label}</div>
        {#if display_value == ""}
            <span class="text-gray-400 italic">no value</span>
        {:else}
            {display_value}
        {/if}
    </div>
{:else}
    <div
        class="rounded-md shadow-md ring-1 ring-inset mb-2 bg-white pb-[5px]
 {hideValidation === false && valid === false
            ? 'ring-red-500 shadow-md'
            : 'ring-indigo-100 shadow-sm'}
"
    >
        <div class="flex justify-between items-center px-2.5 pt-2 gap-2">
            <label
                    for={id}
                    class="block text-xs text-gray-900/50
            {hideValidation === false && valid === false
                        ? 'text-red-600'
                        : 'text-gray-900/50'}
            ">{label}</label
                >
            {#if clearable}
                <span
                    class="text-xs text-gray-900/50 cursor-pointer"
                    on:click={() => clear()}
                >
                    clear
                </span>
            {/if}
        </div>
        <div class="flex-grow relative mx-2">
            <select
                on:change={handleChange}
                bind:value={id}
                on:blur={handleBlur}
                class="
    px-0 py-1
    block
    w-full
    text-base
    font-normal
    text-gray-700
    bg-transparent bg-clip-padding
    border-0 text-gray-900 placeholder:text-gray-400 outline-none
    "
                required
            >
                <option value="" disabled>{instruction}</option>
                {#each options as option}
                    <option value={option.value} disabled={option.disabled}
                        >{option.option}</option
                    >
                {/each}
            </select>
        </div>
        {#if hideValidation == false && valid == false}
            <div class="text-xs bg-red-500 text-white px-3 py-1 rounded-b-md">
                {validation_error}
            </div>
        {/if}
    </div>
{/if}
