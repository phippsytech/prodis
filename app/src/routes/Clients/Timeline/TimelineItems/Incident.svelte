<script>
    export let activity={};

</script>




<div class="flex items-start space-x-3">
    <span class="flex items-center  text-xs opacity-60">{activity.time}</span>
    <div class="flex flex-col items-start space-y-0">
        <span class="bg-indigo-500 text-white border rounded-full text-xs font-bold px-2">
            {#if activity.report_required=="yes"}
                Incident 
            {:else}
                ABC
            {/if} 
            ({activity.impact_ranking}/10)
        </span>
        <span class=" text-xs px-2">

            {#if activity.report_required=="yes"}
                {#if activity.resolved=="no"}<b>NOT RESOLVED</b><br/>{/if}
                {#if activity.management_notified=="no"}<b>Management not notified</b><br/>{/if}
            {/if}


        </span>
                
        <span class=" text-xs px-2">{activity.event_details}</span> <!-- w-60 truncate --->
        <span class="text-xs px-2 opacity-40 whitespace-nowrap" style="font-size:0.7em;">{activity.staff_name}</span>
    </div>
</div>

