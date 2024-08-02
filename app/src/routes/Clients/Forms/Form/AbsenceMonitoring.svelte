<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform
    import Container from '@shared/Container.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import RadioButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    import RadioGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    
    const date = new Date();

    export let staff_id = null;
    export let participant_id = null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        participant_id: participant_id,
        staff_id: staff_id,
        report_type: "AbsenceMonitoring",
        observation: null
    }
    export let readOnly = false;

</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Absence Monitoring</div>

<StaffTimeDate bind:value={form} {readOnly} />
    
<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Why do you think Logan looked absent?</h1>
    <p>You need to describe what you observed.  Example: "Logan was staring into space.  I said 'Hey Logan', no response.  I clicked fingers, no response."</p>
    <textarea placeholder="details..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.observation} {readOnly} ></textarea>

</Container>

<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What was Logan doing at the time?</h1>
    <textarea placeholder="details..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.activity_before_absence} {readOnly} ></textarea>

</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">How long was it before Logan was responsive again?</h1>
    <RadioButtonGroup options={[
        {value: '1', option: '1 min'},
        {value: '3', option: '3 min'},
        {value: '5', option: '5 min'},
        {value: '10', option: "10 min +"}
    ]} bind:value={form.length_of_absence} {readOnly} />

</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What was Logan's behaviour like after the event?</h1>
    <textarea placeholder="details..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.behaviour_after_absence} {readOnly} ></textarea>

</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Any other information required?</h1>
    <p>NA if nothing to add</p>
    <textarea placeholder="details..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.additional_info} {readOnly} ></textarea>

</Container>

