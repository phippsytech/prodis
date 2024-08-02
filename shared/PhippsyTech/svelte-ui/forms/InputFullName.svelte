<script>
    import { makeUniqueId, updateValidity } from "./js/base.js";
    import InputText from "./js/InputText.svelte";
    import ValidityStore from "./js/ValidityStore";
    import { V } from "./js/validation";

    $: validity = $ValidityStore;

    export let value;
    export let label = "Full Name";
    export let validation_error = `A full name is required`;

    let id = makeUniqueId();
    updateValidity(ValidityStore, id, null);

    // Observe the validation state.
    $: valid = validity[id];

    function validate() {
        if (valid != true) {
            V.fullName({
                value: value,
            })
                .then(function () {
                    updateValidity(ValidityStore, id, true);
                })
                .catch(function (reason) {
                    validation_error = reason.error_message;
                    updateValidity(ValidityStore, id, false);
                });
        }
    }
</script>

<InputText
    {label}
    {id}
    bind:value
    on:validate={validate}
    {validation_error}
    placeholder="eg: Alex Smith"
    autocomplete="full_name"
    {...$$props}
/>
