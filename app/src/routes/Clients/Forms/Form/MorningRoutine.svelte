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
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';


    const date = new Date();

    export let  readOnly=false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "MorningRoutine",
        prompt_type: null,
        assisted_lift_opportunity: null,
        assisted_lift_successful: null,
        outcome: null,
    }
    

</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Morning Routine Prompt</div>

<StaffTimeDate bind:value={form}  {readOnly} />



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What types of prompt was used?</h1>

    <RadioGroup options={[
        {value: 'verbal', option: 'Verbal'},
        {value: 'assisted', option: 'Assisted lift'},
    ]} bind:value={form.prompt_type}  {readOnly} />
    





    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Was there a chance to do an assisted lift?</h1>

    <RadioGroup options={[
        {value: 'no', option: "No - Rhys didn't move so couldn't attempt the lift."},
        {value: 'yes', option: 'Yes'},
    ]} bind:value={form.assisted_lift_opportunity}  {readOnly}  />
    




    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Was it successful?</h1>

    <RadioGroup options={[
        {value: 'successful', option: 'Yes'},
        {value: 'unsuccessful', option: "No"}
    ]} bind:value={form.outcome}  {readOnly} />
    

