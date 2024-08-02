<script>
    export let activity={};
</script>

<div class="flex items-start space-x-3">
    <span class="flex items-center  text-xs opacity-60">{activity.time}</span>
    <div class="flex flex-col items-start space-y-0">
        <span class="bg-indigo-500 text-white border rounded-full text-xs font-bold px-2">Wake Up : {activity.temperment}</span>
            <span class=" text-xs px-2 w-60 truncate">
                {#if activity.additional_details}{activity.additional_details}{/if}
            </span>
            <span class="text-xs px-2 opacity-40 whitespace-nowrap" style="font-size:0.7em;">{activity.staff_name}</span>
        
    </div>
</div>
