<script>

    import {replace} from 'svelte-spa-router'
    import Container from '@shared/Container.svelte';
    import Role from "@shared/Role.svelte"
    import {jspa} from "@shared/jspa.js"
    
    import { HouseStore } from '@shared/stores.js';

    let client={}
    
    $: houseStore = $HouseStore;
    $: {
        if(houseStore.id)getClient(houseStore.client_id);
    }

    function getClient(client_id){
        jspa("/Client", "getClient", {id: client_id}).then((result)=>{
            client = result.result;  
        }).catch((error)=>{
            replace("/404");
        })
    }
    

</script>


<Container>       
            
    <h1 class="text-black text-2xl ">Schedule</h1>
    <p>This page is the calendar of events for {client.name}.</p>
    <p>It should include recurring events and one off events.</p>
    <p>It should include medication schedule.</p>
   

