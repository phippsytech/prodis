<script>

    import {makeUniqueId, updateValidity} from './js/base.js'
    import InputText from "./js/InputText.svelte";
    import ValidityStore from './js/ValidityStore';
    import { V } from "./js/validation";

    $: validity = $ValidityStore;

    export let value;
    export let mobile_only=true;
    export let label;
    export let validation_error;

    let id=makeUniqueId();    
    updateValidity(ValidityStore, id, null);

    // Observe the validation state.
    $: valid = validity[id];

    function validate(){

        if (valid!=true){
            V.phone({
                value: JSON.stringify({
                    "phone_number": value,
                    "mobile_only": mobile_only,
                }),
            })
            .then(function(){
                updateValidity(ValidityStore, id, true);
            })
            .catch(function(reason){
                validation_error = reason.error_message;
                updateValidity(ValidityStore, id, false);
            })
            ;
        }

    }

</script>

<InputText 
    {label} 
    id={id}
    
    bind:value={value} 
    
    on:validate={validate}
    validation_error={validation_error}

    inputmode="tel" 
    inputmask="xxxx xxx xxx" 
    placeholder="XXXX XXX XXX" 
    autocomplete="tel" 
    {...$$props} 
/>

