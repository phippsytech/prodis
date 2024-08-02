<script>
        
        
    import {push} from 'svelte-spa-router'
    import NavBar from '../../NavBar.svelte';
    import Container from '@shared/Container.svelte';
    import Breadcrumbs from '@shared/Breadcrumbs.svelte';
    import Footer from '@shared/Footer.svelte';
    import {PlusIcon, XMarkIcon} from 'heroicons-svelte/24/outline'
    import FloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import {jspa} from "@shared/jspa.js"
    // import {formatDate} from "@shared/utilities.js"
    export let params = {}
    import { HouseStore } from '@shared/stores.js';


    const date = new Date();

let ready = false;
    
    

    $: houseStore = $HouseStore;
    
    // const house_id = params.house_id;

    


    let breadcrumbs_path =[];
    $:{
        if (!ready) {
            
        
        if(houseStore.id){
            shift.house_id = houseStore.id;
            breadcrumbs_path = [
                {url:'/roster', name:"Roster"},

            ];    
            getHouseStaff()
            ready=true;
        }
    }
    }
    
    let shifts=[];
    let shift={}

    shift.start_date= `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`

    let staff = [];
    let staffList = [];
    function getHouseStaff(){
    jspa("/SIL/House/Staff", "listHouseStaffByHouseId", {house_id: houseStore.id}).then((result)=>{
        
        staffList = []; // clear the clientList
        staff = result.result;
        staff.push({"staff_id": null, "name": "[Unassigned]", id: null})
        staff = staff;
        
        staff.sort(function(a, b) { 
            const nameA = a.name.toUpperCase(); // ignore upper and lowercase
            const nameB = b.name.toUpperCase(); // ignore upper and lowercase
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0; // names must be equal
        });
        
        staff.forEach(staffer=>{ 
            let options ={
                option:staffer.name,
                value: staffer.staff_id, 
                selected: false,
            }
            if (staffer.staff_id == shift.staff_id) options.selected = true;
            if (staffer.archived!=1) staffList.push(options) 
        })
        staffList = staffList;
    });

    }

    function getStafferName(staff_id) {
  const staffer = staffList.find(s => s.value === staff_id);
  return staffer ? staffer.option : null;
}



        function addShift(){
            // const new_shift = { ...shift };
            // shifts = [...shifts,new_shift]

            jspa("/SIL/House/Roster", "addShift", shift).then((result)=>{
                let new_shift = result.result.result;
                shifts = [...shifts,new_shift]
                shift.start_time=null;
                shift.end_time=null;
                // shift.kms=null;
            });

        }

     

</script>


<NavBar />
<Container>       
    <Breadcrumbs path={breadcrumbs_path} target="Add Shifts" />

    

        <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Staff
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Start
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                End
              </th>
              <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Kms
              </th> -->
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-200">
        {#each shifts as s, index}

        
<tr>
<td class="px-6 py-4 whitespace-nowrap">
            {getStafferName(s.staff_id)}
</td>
<td class="px-6 py-4 whitespace-nowrap">
            {s.start_date}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {s.start_time}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {s.end_time}
        </td>
        <!-- <td class="px-6 py-4 whitespace-nowrap">
            {s.kms}
        </td> -->
</tr>

        {/each}
</tbody>
</table>
    


           
        
        <div class="flex justify-between ">    
    <FloatingSelect
    on:change
    bind:value={shift.staff_id}
    label="Staffer"
    instruction="Choose staffer"
    options={staffList}  
    hideValidation={true}
/> 
<FloatingDateSelect label="Start Date" bind:value={shift.start_date} />
<FloatingTime label="Shift Start" bind:value={shift.start_time} />
<FloatingTime label="Shift End" bind:value={shift.end_time} />
<!-- <FloatingInput label="kms" bind:value={shift.kms} /> -->
</div>
<div class="flex justify-end ">
    <button on:click={()=>addShift()}  type="button" class="inline-block px-6 py-2 border-2 border-gray-300 text-gray-500 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out"> Add </button>

</div>

<!-- <div class="flex justify-between ">
    <span></span>
    <Button on:click={()=>addHouseStaffer()} label="Add"  />
</div> -->

    
    

    
<Footer />