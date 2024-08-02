<script>
    export let activity={};

    let fluid_types={
        softdrink: 'Softdrink',
        juice: 'Juice',
        water: 'Water',
        milk: 'Milk',
        hot_drink: 'Hot Drink'
    }

    let fluid_volumes={
        sip: 'Sip (50mls)',
        small: 'Small Cup (150mls)',
        large: 'Large Cup (300mls)',
        bottle: '600ml bottle',
    }

    let food_options={
        meal: 'Meal',
        snack: 'Snack',
        mother: 'Food eaten while in mums care',
    }

</script>




<div class="flex items-start space-x-3">
<span class="flex items-center  text-xs opacity-60">{activity.time}</span>
<div class="flex flex-col items-start space-y-0">
    <span class="bg-indigo-500 text-white border rounded-full text-xs font-bold px-2">Food &amp; Fluid Intake</span>
        <span class=" text-xs px-2">
            <b>{food_options[activity.food]}</b><br />
            {#if activity.ingesting.includes('fluid')}
                {fluid_types[activity.fluid_type]}: {fluid_volumes[activity.fluid_volume]}<br />
            {/if}

            {#if activity.ingesting.includes('food')}
                 {activity.food_type} : {activity.food_volume}<br />
            {/if}

            {#if activity.positive_engagements}<b>Positive Engagements:</b> {activity.positive_engagements}<br/>{/if}
            {#if activity.negative_moments}<b>Negative Moments:</b> {activity.negative_moments}<br/>{/if}
            {#if activity.additional_details}<b>Other: </b> {activity.additional_details}<br/>{/if}
        </span>
        <span class="text-xs px-2 opacity-40 whitespace-nowrap" style="font-size:0.7em;">{activity.staff_name}</span>
    </div>
</div>

