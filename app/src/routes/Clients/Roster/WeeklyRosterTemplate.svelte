<script>
    import {onMount} from 'svelte'
    import NavBar from '../../NavBar.svelte';
    import Container from '@shared/Container.svelte';
    import Breadcrumbs from '@shared/Breadcrumbs.svelte';
    import Footer from '@shared/Footer.svelte';
    import {PlusIcon, XMarkIcon} from 'heroicons-svelte/24/outline'
    import { HouseStore } from '@shared/stores.js';

    import {jspa} from "@shared/jspa.js"

    // You need to define the component prop "params"
    export let params = {}
    
    $: houseStore = $HouseStore;

    let shifts = []
    let stored_shifts = []
    let breadcrumbs_path = [];
    let house = {};
    let mounted = false;
    let show = false;
    
    let template_id;

    let staff = [];

    $: {
        if(mounted){
            show = !(JSON.stringify(shifts) === JSON.stringify(stored_shifts))
        }
    }

    $:{
        if(houseStore.id){
            breadcrumbs_path = [
                {url:'/roster', name:"Roster"},
            ];    
            // getHouseStaff()
        }
    }
    


    onMount(()=>{

        // jspa("/SIL/House", "getHouse", {id: houseStore.id}).then((result)=>{
        //     house = result.result;
        //     breadcrumbs_path = [
        //         {url:'/', name:'SIL'},
        //         {url:'/sil/'+house_id, name:house.name+" House"},
        //     ];
        // })

        jspa("/SIL/House/Staff", "listHouseStaffByHouseId", {house_id: houseStore.id}).then((result)=>{
            staff = result.result;
            staff.push({"staff_id": null, "name": "[Unassigned]", id: null})
            staff = staff;
        });



        jspa("/SIL/House/Roster/Template", "getTemplate", {house_id: houseStore.id}).then((result)=>{
            shifts = result.result.template;
            template_id = result.result.id;
        }).catch((error)=>{
        }).finally(()=>{
            // Make a copy of the object
            stored_shifts = JSON.parse(JSON.stringify(shifts));
        });
        mounted = true;
    })    

    function update(){
        jspa("/SIL/House/Roster/Template", "updateTemplate", {id:template_id, template: shifts}).then((result)=>{
            stored_shifts = JSON.parse(JSON.stringify(shifts));
        })
    }

    function undo(){
        shifts = JSON.parse(JSON.stringify(stored_shifts));
    }

    // let staff=[  
    //     {"id":1, "name":"Lee"},
    //     {"id":2, "name":"Marcus"},
    //     {"id":3, "name":"Steve"},
    //     {"id":4, "name":"Kym"},
    //     {"id":5, "name":"Paul"},
    //     {"id":6, "name":"Nathan"},
    //     {"id":7, "name":"Dave"}
    // ]


    

       
    

    function addEmptyShift(day) {
        let dayObj = shifts.find(obj => obj.day === day);
        dayObj.shifts.push({"start": null, "end": null, "staff_id": null});
        shifts = shifts.map(obj => obj.day === day ? dayObj : obj);
    }

    function deleteShift(day, shiftIndex) {
        const dayObj = shifts.find(d => d.day === day);
        dayObj.shifts.splice(shiftIndex, 1);
        shifts = shifts.map(obj => obj.day === day ? dayObj : obj);
    }

    function createTemplate(){
        jspa("/SIL/House/Roster/Template", "createTemplate", {house_id: house_id}).then((result)=>{
            template_id = result.result.id;
            shifts = result.result.template;
            stored_shifts = JSON.parse(JSON.stringify(shifts));
        })
    }


</script>

<NavBar />
<Container>       
    <Breadcrumbs path={breadcrumbs_path} target="Weekly Roster Template" />
    
    Use this template to manage the shifts that are generated when you add a new week to the roster.
    Important to note that this is a template only. It does not change any data in the roster.
    

        <table>
            {#each shifts as shift}
                <tr><td class="font-bold pt-4 flex justify-start " colspan="3">{shift.day} <div on:click={()=>addEmptyShift(shift.day)}><PlusIcon  class="w-6 h-6"  /></div></td></tr>
                {#each shift.shifts as shift_item,shiftIndex}
                    <tr>
                        <td><input type="text" bind:value={shift_item.start}/></td>
                        <td><input type="text" bind:value={shift_item.end}/></td>
                        <td><select bind:value={shift_item.staff_id}>{#each staff as staffer}<option value={staffer.staff_id}>{staffer.name}</option>{/each}</select></td>
                        <td><div on:click={()=>deleteShift(shift.day, shiftIndex)}><XMarkIcon  class="w-4 h-4"  /></div></td>
                    </tr>
                {/each}
                {:else}
                <button on:click={()=>createTemplate()} type="button" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Generate Starter Template</button>
            {/each}
            
        </table>
    

<Footer />



{#if show}
    <div class="p-5"></div>
{/if}
<div  class="fixed bottom-0 w-full transition duration-500 ease-in-out transform {(show==true)?'translate-y-0':'translate-y-full'}" style="background:#f3f1f4"
> 
    <div class="md:container mx-auto py-4 px-6 md:px-40 " >
        <div class="flex justify-between ">
            <span class="text-sm">Your have unsaved changes.</span>
            <button on:click={()=>undo()} type="button" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Undo</button>
            <button on:click={()=>update()} type="button" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Update</button>

        </div>
    </div>
</div>