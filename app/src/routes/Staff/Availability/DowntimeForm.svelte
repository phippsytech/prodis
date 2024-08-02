<script>
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    
    export let event={};
    event.recurring="once"
    event.all_day=true

    const date = new Date();
    
    let min_date = date.toLocaleString().slice(0, 10).split('/').reverse().join('-');

</script>


<FloatingInput bind:value={event.reason} label="Reason for Unavailability" placeholder="eg: Sick, Vacation, Appointment, School" />

<div class="p-2 mb-2">
    <h1 class="text-gray-800 text-1xl font-medium mt-0  mb-2">Is this a recurring unavailability?</h1>
<RadioButtonGroup options={[
    {value: 'once', option: 'No'},
    {value: 'weekly', option: 'Weekly'},
    {value: 'fortnightly', option: 'Fortnightly'},

]} bind:value={event.recurring} />

</div>
<!-- 
<FloatingSelect
    bind:value={event.recurring}
    label="Recurring?"
    instruction="Choose recurrence"
    options={recurringOptions}  
/> -->


<div class="gap-2 columns-1 lg:columns-2">
    
    
    <FloatingDate bind:value={event.start_date} label="Start Date" placeholder="eg: 30 Sep 2022" min={min_date} />
    <FloatingDate bind:value={event.end_date} label="End Date" placeholder="eg: 30 Sep 2022" bind:min={event.start_date} />
    
  </div>



 
<div class="p-2 mb-2">
    <RadioButtonGroup options={[
        
        {value: true, option: 'Whole Day'},
        {value: false, option: 'Part of Day'},
    
    ]} bind:value={event.all_day} />

</div>
{#if !event.all_day}

<div class="">
    <h1 class="text-gray-800 text-1xl font-medium mt-0 px-2  mb-2">Unavailable between:</h1>
    </div>

<div class="gap-2 columns-1 lg:columns-2">
<FloatingTime bind:value={event.start_time} label="Start Time" placeholder="eg: 4pm" mode="time"  />
<FloatingTime bind:value={event.end_time} label="End Time" placeholder="eg: 6pm" mode="time"  />
</div>
{/if}