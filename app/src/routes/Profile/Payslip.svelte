<script>
    import { onMount } from 'svelte';
    
    import { UserStore, BreadcrumbStore } from '@shared/stores.js';
    
    import {jspa} from "@shared/jspa.js"
    // import Credentials from './Credentials/Credentials.svelte';
    
    import Container from '@shared/Container.svelte';
    
    import {} from '@shared/stores.js';
        
    
    $: userStore = $UserStore;

    // let staff_id = params.staff_id

    // export let staff_id = null
    let userComponent;
    let user={};

    let loaded = false;




    onMount(async () => {

        if(userStore.id){

            BreadcrumbStore.set({path:[
                {name:userStore.name, url:null},
            ]})
        }
       
    });

function handleLoaded(event){
    user = event.detail.user;
    // loaded = true;
}

function handleChanged(event){
    user = event.detail.user;
    // loaded = true;
}


</script>


    
    <!-- <div class="mx-auto  px-4 sm:px-6 lg:px-8 mt-8">
    
    
    </div>
     -->
    
    
    
     
     <div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2" style="color:#220055;">{$UserStore.name}</div>

     <Container>
        Current Payslip
     </Container>



    






