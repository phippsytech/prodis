<script>
    import { makeUniqueId, updateValidity } from "../js/base.js";
    import Masker from "../js/Masker.js";
    import { createEventDispatcher } from "svelte";
    import { onMount } from "svelte";
    import ValidityStore from "../js/ValidityStore";
    import { V } from "../js/validation";

    $: validity = $ValidityStore;

    // Observe the validation state.
    // $: valid = validity[id];
    export let valid = true;

    // TODO: DID YOU KNOW THERE IS A VALIDITY STATE ON HTML INPUTS? IT IS CALLED validity

    export let inputmask = null; // rename to pattern or mask?
    export let label = "Label";
    export let value; //=null;
    export let placeholder = null;
    export let validation_error = `${label} is required`;

    // export let autocomplete="off";
    export let autofocus = false;
    // export let disabled= false;
    export let readOnly = false;
    // export let required=false;

    let value_history = [];
    let last_value = null;
    let last_valid_value = null;
    let last_invalid_value = null;

    // if the validity changes remember the value that caused it.
    //    $: if(value) {
    //         value_history.push(value);
    //         if (value_history.length>2) value_history.shift();
    //         last_value = value_history[0];
    //         if(valid===true) last_valid_value=last_value;
    //         if(valid===false) last_invalid_value=last_value;
    //     }

    let id;
    let me = false; // me is used to identify if this component is being called directly
    if (!id) {
        id = makeUniqueId();
        me = true;
    }

    const dispatch = createEventDispatcher();

    let element;
    onMount(() => {
        if (!readOnly) {
            if (inputmask) {
                const masker = new Masker(
                    {
                        mask: inputmask,
                        value: "",
                        placeholderSymbol: "x",
                        allowedCharacters: "[0-9]",
                    },
                    element,
                );
                element.oninput = () => {
                    masker.onInput();
                    value = masker.output;
                };
            }

            if (autofocus) {
                element.focus();
            }
        }
    });

    function handleBlur() {
        if (valid == null) {
            validate();
        }
    }

    function handleKeyUp(event) {
        if (valid != null && last_value != value) {
            updateValidity(ValidityStore, id, null);
        }
    }

    function validate() {
        if (!isEmpty(value)) {
            if (valid == null) {
                if (last_valid_value == value) {
                    updateValidity(ValidityStore, id, true);
                    return;
                }
                if (last_invalid_value == value) {
                    updateValidity(ValidityStore, id, false);
                    return;
                }
            }
            if (me) {
                // updateValidity(ValidityStore, id, false);
            } else {
                dispatch("validate", {});
            }
        }

        // if the value is empty don't validate.
    }

    function isEmpty(string) {
        if (string === "" || string == null) return true;
        return false;
    }
</script>

{#if readOnly}
    {#if value}
        <div class="bg-white rounded-sm px-4 py-2 mb-2">
            <div class="text-xs opacity-50">{label}</div>
            {value}
        </div>
    {/if}
{:else}
    <div
        class="rounded-md px-3 pb-1.5 pt-2.5 shadow-sm ring-1 ring-inset ring-indigo-100 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
    >
        <label for={id} class="block text-xs text-gray-900/50">{label}</label>
        <input
            {id}
            type="text"
            on:keyup
            bind:this={element}
            {placeholder}
            bind:value
            class="masker block w-full border-0 p-0 text-gray-900 placeholder:text-gray-400 sm:text-sm sm:leading-6 outline-none invalid
            {valid == false ? ' text-red-500 ' : ''}"
            {...$$props}
        />
    </div>
{/if}
