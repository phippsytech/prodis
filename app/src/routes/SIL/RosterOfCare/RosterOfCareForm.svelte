<script>
    
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import Toggle from '@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte';
    import {jspa} from "@shared/jspa.js"
    import {formatDate} from "@shared/utilities.js"

        
    export let props;

    
</script>

    


<FloatingInput bind:value={props.participant_name} label="Name of Participant" />
