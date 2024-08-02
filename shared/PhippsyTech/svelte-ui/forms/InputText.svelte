<script>
    import { makeUniqueId, updateValidity } from "../js/base.js";
    import Masker from "../js/Masker.js";
    import { createEventDispatcher } from "svelte";
    import { onMount } from "svelte";
    import ValidityStore from "../js/ValidityStore";

    $: validity = $ValidityStore;

    // Observe the validation state.
    $: valid = validity[id];

    // TODO: DID YOU KNOW THERE IS A VALIDITY STATE ON HTML INPUTS? IT IS CALLED validity

    export let id;
    export let inputmask = null; // rename to pattern or mask?
    export let label = "Label";
    export let value = null;
    export let placeholder = null;
    export let validation_error = `${label} is required`;
    export let autocomplete = "off";
    export let autofocus = false;
    export let disabled = false;
    export let readOnly = false;
    export let required = false;

    let value_history = [];
    let last_value = null;
    let last_valid_value = null;
    let last_invalid_value = null;

    // if the validity changes remember the value that caused it.
    $: if (value) {
        value_history.push(value);
        if (value_history.length > 2) value_history.shift();
        last_value = value_history[0];
        if (valid === true) last_valid_value = last_value;
        if (valid === false) last_invalid_value = last_value;
    }

    // me is used to identify if this component is being called directly
    let me = false;
    if (!id) {
        id = makeUniqueId();
        me = true;
    }

    const dispatch = createEventDispatcher();

    let element;
    onMount(() => {
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

<div class="form-floating m-4 flex-grow">
    <input
        {id}
        bind:this={element}
        bind:value
        on:blur|stopPropagation={handleBlur}
        on:keyup={handleKeyUp}
        autocomplete={valid == true ? "new-password" : autocomplete}
        novalidate
        class="masker form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-indigo-100 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none {valid ==
        true
            ? ' is-valid '
            : ''} {valid == false ? ' is-invalid ' : ''} "
        {...$$props}
    />
    <!-- TEST IF PROPS ARE BEING PASSED.  IF THEY ARE NOT, YOU MIGHT NEED TO USE $$Props (Captial Letter)  This might be a typescript issue that isn't relevant. -->
    <label>{label}</label>
    {#if valid == false}<div class="invalid-feedback">
            {validation_error}
        </div>{/if}
</div>

<style>
    .form-floating ::-webkit-input-placeholder {
        opacity: 0;
        transition: inherit;
        color: #ccc;
    }

    .form-floating ::-moz-placeholder {
        opacity: 0;
        transition: inherit;
        color: #ccc;
    }

    .form-floating input:focus::-webkit-input-placeholder,
    .form-floating textarea:focus::-webkit-input-placeholder {
        opacity: 1;
    }
</style>
