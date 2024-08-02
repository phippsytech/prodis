<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform

    
    import Container from '@shared/Container.svelte';
    
    import ReportPanel from '@shared/ReportPanel.svelte'
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    import RadioGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte';
    import RadioButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    
    const date = new Date();    
    
    export let readOnly=false;
    export let staff_id= null;
    export let participant_id= null;

    export let form={
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "WakeUp",
        temperment: "Neutral", // Happy, Neutral, Unhappy
        additional_details: null
    };


    
</script>
<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Wake Up</div>
   

    <StaffTimeDate bind:value={form} labels={{
        date:"The date when the participant woke up?", 
        time:"What time did the participant wake up?"
}} {readOnly} />





  
    <h1 class="text-gray-700 text-1xl font-semibold mt-0 mb-4 mb-2">Did the participant wake up happy, neutral or unhappy?</h1>

          
    <RadioButtonGroup bind:value={form.temperment} options={[
        {value: 'Happy', option: 'Happy'},
        {value: 'Neutral', option: 'Neutral'},
        {value: 'Unhappy', option: 'Unhappy'}
      ]}  {readOnly} />





    <h1 class="text-gray-700 text-1xl font-semibold mt-0  mb-2">Any comments?</h1>
    <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.additional_details} ></textarea>

