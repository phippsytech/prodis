<script>
    // THIS SLIDE is not going to work unless you use LI elements instead of tables.
    import {slide,fade} from 'svelte/transition';
    import {quintOut} from 'svelte/easing';
    import {formatDate, convertToHoursAndMinutes,convertMinutesToHoursAndMinutes, convertDecimalHoursToHoursAndMinutes} from "@shared/utilities.js"
    
    import KPIViewParticipantSessions from "./KPIViewParticipantSessions.svelte";
export let line_items
let selected_client_id

let filtered_items=[];


    function selectClient(client_id){
        if (selected_client_id == client_id){
            selected_client_id = null;
            return;
        }
        selected_client_id=client_id;
        filtered_items = line_items.filter(item => item.client_id === client_id);
    }

$: grouped_items = Object.values(line_items.reduce((acc, item) => {
            const key = `${item.client_name}`;
            if (!acc[key]) {
                acc[key] = {
                client_name: item.client_name,
                client_id: item.client_id,
                session_duration: 0
                };
            }
            acc[key].session_duration += +item.session_duration;
            return acc;
            }, {}));

</script>


 <!-- Header -->
 <div in:fade={{ delay:250, duration: 250 }}  out:fade={{duration:50}} class="flex justify-between items-center py-1 text-xs font-medium text-gray-500" >
    <div>Client</div>
    <div>Time</div>
</div>


<ul class="border-y border-gray-200 w-full mb-4 text-sm">
        
        {#each grouped_items as item, index}
        <li in:slide|global="{{duration: 250,easing:quintOut }}" out:slide|global="{{duration: 250,easing:quintOut }}"  on:click={()=>selectClient(item.client_id)} class="border-b border-gray-200 py-1 hover:bg-indigo-50 cursor-pointer ">
            <div class="flex justify-between">
                <div class="{(item.client_id==selected_client_id)?'font-bold':''}" >{item.client_name}</div>                
                <div class=" text-right" > {@html convertDecimalHoursToHoursAndMinutes(item.session_duration/60)}</div>
            </div>
            {#if item.client_id==selected_client_id}
            <KPIViewParticipantSessions line_items={filtered_items} />
            {/if}


        </li>      
    
         
        {/each}
</ul>