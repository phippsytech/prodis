<script>
    export let activity={};

    let escalation_types={
        doctor: 'Locum / GP',
        saas: 'SAAS - Ambulance',
        sapol: 'SAPOL',
        hospital: 'Hospital',
        house_lead: 'House Lead',
        manager: 'Manager',
    }

</script>

<div class="flex items-start space-x-3">
<span class="flex items-center  text-xs opacity-60">{activity.time}</span>
<div class="flex flex-col items-start space-y-0">
    <span class="bg-indigo-500 text-white border rounded-full text-xs font-bold px-2">Injury ({activity.inury_severity}/5)</span>
        <span class=" text-xs px-2">
            {#if activity.escalation_required=="yes"}<b>Escalated:</b>{escalation_types[activity.escalation_type]}<br/>{/if}
            {activity.injury_cause}<br/>
        </span>
        <span class="text-xs px-2 opacity-40 whitespace-nowrap" style="font-size:0.7em;">{activity.staff_name}</span>
    </div>
</div>

