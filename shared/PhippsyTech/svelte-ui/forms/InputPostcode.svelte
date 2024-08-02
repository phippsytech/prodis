<script>
    import { makeUniqueId, updateValidity } from "./js/base.js";
    import InputText from "./js/InputText.svelte";
    import ValidityStore from "./js/ValidityStore";
    import { V } from "./js/validation";

    $: validity = $ValidityStore;

    export let value;
    export let state = null;
    export let suburbs = null;
    export let label;
    export let validation_error = `You're not that old`;

    let id = makeUniqueId();
    updateValidity(ValidityStore, id, null);

    // Observe the validation state.
    $: valid = validity[id];

    function validate() {
        if (valid != true) {
            V.postcode({
                value: value,
            })
                .then(function (result) {
                    if (result.valid == true) {
                        state = result.state;
                        suburbs = result.suburbs;
                        updateValidity(ValidityStore, id, true);
                    } else {
                        validation_error = reason.error_message;
                        updateValidity(ValidityStore, id, false);
                    }
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
    inputmode="numeric"
    inputmask="xxxx"
    placeholder="eg: 2000"
    autocomplete="postcode"
    {...$$props}
/>
