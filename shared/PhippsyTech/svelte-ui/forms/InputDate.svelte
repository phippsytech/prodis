<script>

    import {makeUniqueId, updateValidity} from '../js/base.js'
    import InputText from "./InputText.svelte";
    import ValidityStore from '../js/ValidityStore';
    import { V } from "../js/validation";

    $: validity = $ValidityStore;

    export let value;
    export let label;
    export let validation_error=`You're not that old`;

    let id=makeUniqueId();    
    updateValidity(ValidityStore, id, null);

    // Observe the validation state.
    $: valid = validity[id];

    function validate(){

        if (valid!=true){
            V.date({
                value: value,
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
    inputmode="numeric" 
    inputmask="xx/xx/xxxx" 
    placeholder="dd/mm/yyyy" 
    autocomplete="name" 
    {...$$props} 
/>