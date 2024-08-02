<script>
    import {getElapsedTime} from '@shared/utilities.js'

    export let activity={};

    let elapsed_time

    if(activity.time_asleep){
        elapsed_time = getElapsedTime(activity.time, activity.time_asleep);
    }

</script>


<div class="flex items-start space-x-3">
    <span class="flex items-center  text-xs opacity-60">{activity.time}</span>
    <div class="flex flex-col items-start space-y-0">
        <span class="bg-indigo-500 text-white border rounded-full text-xs font-bold px-2">Sleep Disturbance</span>
            <span class=" text-xs px-2">{#if elapsed_time}<b>{elapsed_time} mins</b> {activity.time} - {activity.time_asleep}{:else}<b>{activity.time} - Did not return to bed</b>{/if}</span>
            <span class=" text-xs px-2 w-60 truncate">{activity.reason}</span>
            <span class="text-xs px-2 opacity-40 whitespace-nowrap" style="font-size:0.7em;">{activity.staff_name}</span>
        
    </div>
</div>
