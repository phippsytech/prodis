<script>
        export let activity={};
</script>

<div class="flex items-start space-x-3">
    <span class="flex items-center  text-xs opacity-60">{activity.time}</span>
    <div class="flex flex-col items-start space-y-0">
        <span class="bg-indigo-500 text-white border rounded-full text-xs font-bold px-2">End of Shift: <span class="font-normal">{activity.staff_name}</span></span>
            <span class=" text-xs px-2">
                {#if activity.positive_engagements}<b>Positive Engagements:</b> {activity.positive_engagements}<br/>{/if}
                {#if activity.negative_moments}<b>Negative Moments:</b> {activity.negative_moments}<br/>{/if}
                {#if activity.additional_details}<b>Other: </b> {activity.additional_details}<br/>{/if}
            </span>
            <!-- <span class="text-xs px-2 opacity-40 whitespace-nowrap" style="font-size:0.7em;">{activity.staff_name}</span> -->
        
    </div>
</div>