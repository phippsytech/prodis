<script>
    import {makeUniqueId, updateValidity} from '../js/base.js'
    import FloatingInput from "./FloatingInput.svelte.js";
    import ValidityStore from '../js/ValidityStore.js';
    import { V } from "../js/validation.js";

    export let value;
    export let label;
    export let validation_error=`Invalid Date`;

    let display=null; // this is the visual version of the date

    let id=makeUniqueId();    
    updateValidity(ValidityStore, id, null);

    $: validity = $ValidityStore;
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

    function getDisplayDate(timestamp){
        // remember, this is a timestamp from PHP, you need to * 1000
        var date = new Date(timestamp*1000);
        var formattedDate = date.toLocaleDateString("en-AU", {
            day: "numeric",
            month: "numeric", // short
            year: "numeric"
        });
        return formattedDate;
    }

    
    $: {
        
        if(display == null && value){
            display = getDisplayDate(value);
        }

        if(display && display.length == 10){
            let parts = display.split('/');
            let date = new Date(parseInt(parts[2], 10), 
            parseInt(parts[1], 10)-1, 
            parseInt(parts[0], 10));
            let timestamp = date.getTime() / 1000;  // preparing timestamp for php
            if (value != timestamp){
                value  = timestamp;
            }
        }
    }
    

</script>

<FloatingInput
    {label} 
    id={id}
    
     bind:value={display}
    
    on:validate={validate}
    validation_error={validation_error}

    
    inputmode="numeric" 
    autocomplete="off"
    
    inputmask="xx/xx/xxxx" 
    placeholder="dd/mm/yyyy" 
    
    
/>

