<script>
    import {location, querystring} from '@app/../node_modules/svelte-spa-router'
    import { AppData, StafferStore, HouseStore, RolesStore } from '../stores.js';
    
    $: appData  = JSON.parse($AppData)
    $: stafferStore = $StafferStore;
    $: houseStore = $HouseStore;
    $: rolesStore = $RolesStore;

    $: {
        $location;
        $querystring;
    }
    $: checkRoadblocks(appData, stafferStore, houseStore, rolesStore);
    
    export let roadblockChecks = [];

    let showRoadblock = false;
    let roadblock = null;
    
    function checkRoadblocks(appData, stafferStore, houseStore, rolesStore){
        roadblock = null;
        (async ()=> {
            try{
                for (let checkRoadblock of roadblockChecks) {
                    await checkRoadblock();
                }
                showRoadblock = false;
                return true;
            }catch(component){
                showRoadblock = true;
                roadblock = component
            }
        })();
    }
    
</script>

{#if showRoadblock === true }
    <svelte:component this={roadblock}  />
{:else}
    <slot />
{/if}