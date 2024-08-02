<script>
    export let activity={};

    let contact_methods={
        verbal: 'Call',
        sms: 'SMS',
        visit: 'Visit',
        email: 'Email',
        personal_number: "Call/SMS Personal",
        outing_with_staff: 'Outing',
        outing_without_staff: 'Outing (no staff)',
    }

</script>




<div class="flex items-start space-x-3">
<span class="flex items-center  text-xs opacity-60">{activity.time}</span>
<div class="flex flex-col items-start space-y-0">
    <span class="bg-indigo-500 text-white border rounded-full text-xs font-bold px-2">Parental Contact : {contact_methods[activity.contact_method]}</span>
        <span class=" text-xs px-2">
            {#if activity.reason}{activity.reason}{/if}
            {#if activity.contact_method=="outing_with_staff" ||  activity.contact_method=="outing_without_staff"}
            {#if activity.outing_destination}<b>Destination:</b>{activity.outing_destination}<br/>{/if}
            {#if activity.outing_return_time}<b>ETA:</b> {activity.outing_return_time}<br/>{/if}
            {/if}
            {#if activity.contact_impact == 'negative'}
            <b>Negative Impact</b><br/>
                {activity.negative_impact_comments}<br />
            {/if}
        </span>
        <span class="text-xs px-2 opacity-40 whitespace-nowrap" style="font-size:0.7em;">{activity.staff_name}</span>
    </div>
</div>

