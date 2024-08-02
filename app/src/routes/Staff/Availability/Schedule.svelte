<script>
    
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
    import { PlusIcon, XMarkIcon } from 'heroicons-svelte/24/outline'
// import {jspa} from "./jspa.js"

let new_item={};
        
export let schedule =[
];




$: sorted_schedule = schedule.map(c => {
    var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    var day_number = days.indexOf(c.day)

    var time = c.start_time.substring(0,c.start_time.length - 2);
    var am_pm = c.start_time.slice(-2);
    var hours = parseInt(time.split(':')[0]);
    var minutes = parseInt(time.split(':')[1]);

    if (hours === 12 && am_pm.toLowerCase() === 'am') {
        hours = 0;
    } else if (hours < 12 && am_pm.toLowerCase() === 'pm') {
        hours += 12;
    }

    // save hours and minutes
    c.day_number = day_number
    c.hours = hours;
    c.minutes = minutes;

    return c;
}).sort((a,b) => {
    return (a.day_number * 10000 + a.hours * 100 + a.minutes) - (b.day_number * 10000 + b.hours * 100 + b.minutes); 
});


function addNewSchedule(thing, i){
    schedule = [...schedule, { ...thing }];
}

function deleteSchedule(i){
    schedule = schedule.filter((item,index) => index !== i);
}
// jspa("http://localhost:8000/event", "getAll", {}).then((result)=>{
//     events = result.result;
// })

</script>

<style>
  td:focus-visible {
    outline:1px solid rgb(96,165,250)!important;
  }
</style>



<div class="flex flex-col">
    <div class=" sm:-mx-2 lg:-mx-8">
      <div class="py-2 sm:px-2 lg:px-8">
        <div class="">
          <table class="">
            <thead class="border-b">
              <tr>
                
                <th scope="col" class="text-sm font-medium  px-2 py-4 text-left">
                  Day
                </th>
                <th scope="col" class="text-sm font-medium  px-2 py-4 text-left">
                  Start Time
                </th>
                <th scope="col" class="text-sm font-medium  px-2 py-4 text-left">
                  End Time
                </th>
                <th scope="col" class="text-sm font-medium  px-2 py-4 text-left">
                    
                  </th>
              </tr>
            </thead>
            <tbody>
                {#each sorted_schedule as item, i (i)}

                <tr class="border-b" >
                    
                    <td class="text-sm  font-light px-2 py-4 "  >
                        {item.day}
                    </td>
                    <td class="text-sm  font-light px-2 py-4 "   >
                        {item.start_time}
                    </td>
                    <td class="text-sm  font-light px-2 py-4 " >
                        {item.end_time}
                    </td>
                    <td class="text-sm  font-light px-2 py-4 " 
                    >
                    <button on:click={()=>deleteSchedule(i)}><XMarkIcon class="inline h-4 w-4" /></button>
                    </td>
                  </tr>
    
                {/each}
       

                <tr class="border-b bg-gray-800">
                    
                    <td class="text-sm  font-light px-2 py-4 ">
                        
                        <select class="bg-gray-800 text-white" bind:value={new_item.day} >
                            <option selected disabled >Select Day</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                            <option>Saturday</option>
                            <option>Sunday</option>
                        </select>
                    </td>
                    <td class="text-sm  font-light px-2 py-4 " >
                        <FloatingDateSelect bind:value={new_item.start_time} label="Start Time" placeholder="eg: 4pm" mode="time" class="bg-transparent text-white" />
                        
                    </td>
                    <td class="text-sm  font-light px-2 py-4 " >
                        <FloatingDateSelect bind:value={new_item.end_time} label="End Time" placeholder="eg: 6pm" mode="time" class="bg-transparent text-white" />
                        
                    </td>
                    <td class="text-sm  font-light px-2 py-4 " >
                        <button on:click={()=>addNewSchedule(new_item)}><PlusIcon  class="inline h-4 w-4" /></button>
                    </td>
                  </tr>


              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

