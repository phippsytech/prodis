<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform

    
    import Container from '@shared/Container.svelte';
    
    import ReportPanel from '@shared/ReportPanel.svelte'
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    
    import RadioButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte';
    import CheckboxButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    
    
    const date = new Date();

    export let readOnly = false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "InternetMonitoring",
        day_type: null,
        period_status: null,
        location: null,
        using_internet: null,
        time_spent: null,
        additional_info: null
    }
    

    function isWeekday() {
        const day = new Date().getDay();
        return day !== 0 && day !== 6;
    }

    $:{
        if(isWeekday()){
            form.day_type = "weekday"
        } else {
            form.day_type = "weekend"   
        }
    }

    $:{
        if(form.using_internet == "no"){
            form.time_spent = null
        } else {
            // form.time_spent = 0 
        }
    }

</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Internet Monitoring</div>
<StaffTimeDate bind:value={form}  {readOnly} />



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Today is a ...</h1>

    <RadioButtonGroup options={[
        {value: 'weekday', option: 'Weekday'},
        {value: 'weekend', option: 'Weekend'},
    ]} bind:value={form.day_type}  {readOnly} />




    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What are you reporting on?</h1>

    <RadioButtonGroup options={[
        {value: 'start', option: 'Start of Internet access period'},
        {value: 'end', option: 'End of Internet access period'},
    ]} bind:value={form.period_status}  {readOnly} />


{#if form.period_status == "start"}

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Start of access</h1>

    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Where are you right now?</h1>
    
        <RadioButtonGroup options={[
            {value: 'skatepark', option: 'Skatepark'},
            {value: 'home', option: 'Home'},
            {value: 'community', option: 'Out in the community'},
            {value: 'family', option: 'With family'},
        ]} bind:value={form.location}  {readOnly} />
    


    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Has Rhys started using his internet?</h1>
    
        <RadioButtonGroup options={[
            {value: 'yes', option: 'Yes'},
            {value: 'no', option: 'No'},
        ]} bind:value={form.using_internet}  {readOnly} />
    



{/if}

{#if form.period_status == "end"}

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">End of access</h1>

    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Where are you right now?</h1>
    
        <RadioButtonGroup options={[
            {value: 'skatepark', option: 'Skatepark'},
            {value: 'home', option: 'Home'},
            {value: 'community', option: 'Out in the community'},
            {value: 'family', option: 'With family'},
        ]} bind:value={form.location}  {readOnly} />
    


    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Did Rhys use his internet at all?</h1>
    
        <RadioButtonGroup options={[
            {value: 'yes', option: 'Yes'},
            {value: 'no', option: 'No'},
        ]} bind:value={form.using_internet}  {readOnly} />
    

    {#if form.using_internet=="yes"}
    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Estimate how much time he spent on it?</h1>
    
        <RadioButtonGroup options={[
            {value: '5', option: '5 Minutes'},
            {value: '15', option: '15 Minutes'},
            {value: '30', option: '30 Minutes'},
            {value: '60', option: '1 hour'},
            {value: '90', option: '1 hour 30 minutes'},
            {value: '120', option: '2 hours'},
            {value: '180', option: '3 hours'},
        ]} bind:value={form.time_spent}  {readOnly} />
    
    {/if}
    


{/if}




    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Additional Info</h1>
    <textarea placeholder="write note here" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.additional_info}  {readOnly} ></textarea>




