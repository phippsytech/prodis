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

    const yes_no_options = [
        {value: 'yes', option: 'Yes'},
        {value: 'no', option: 'No'},
    ]

    const contact_impact_options = [
        {value: 'negative', option: 'Negative'},
        {value: 'neutral', option: 'Neutral'},
        {value: 'positive', option: 'Positive'},
    ]

    const contact_method_options=[
        {value: 'verbal', option: 'Verbal call to work phone'},
        {value: 'sms', option: 'SMS to work phone'},
        {value: 'visit', option: 'Visit to site'},
        {value: 'email', option: 'Email to work email'},
        {value: 'personal_number', option: 'Call or sms to personal number'},
        {value: 'outing_with_staff', option: 'Leaving the site with Participant and staff'},
        {value: 'outing_without_staff', option: 'Leaving the site with Participant without staff'},

    ]

    export let readOnly=false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "ParentalContact",
        note: null
    }
    
    

</script>
<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Parental Contact</div>

<StaffTimeDate bind:value={form}  {readOnly} />



    <h1 class="mt-0 mb-2">What was the reason for contact?</h1>
    <textarea placeholder="additional information" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.reason}  {readOnly} ></textarea>



    <h1 class="mt-0 mb-2">What was the outcome you gave them?</h1>
    <textarea placeholder="additional information" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.outcome}  {readOnly} ></textarea>



    <h1 class="mt-0 mb-2">Have you informed the House lead?</h1>
    <RadioButtonGroup options={yes_no_options} bind:value={form.house_lead_informed}  {readOnly} />





    <h1 class="mt-0 mb-2">What effect did this interaction have on the participant or the care team around him?</h1>
    <RadioButtonGroup options={contact_impact_options} bind:value={form.contact_impact}  {readOnly} />



{#if form.contact_impact=="negative"}

    <h1 class="mt-0 mb-2">In what way was the contact negative?</h1>
    <textarea placeholder="additional information" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.negative_impact_comments}  {readOnly} ></textarea>

{/if}



    <h1 class="mt-0 mb-2">How did they contact you?</h1>
    <RadioButtonGroup options={contact_method_options} bind:value={form.contact_method}  {readOnly} />



{#if form.contact_method=="outing_with_staff"}

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Outing with parent and staff</h1>
    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Where are you heading?</h1>
        <textarea placeholder="write note here" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.outing_destination}  {readOnly} ></textarea>
    
    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">have you completed a Risk Assessment?</h1>
        <RadioButtonGroup options={yes_no_options} bind:value={form.risk_assessment_completed}  {readOnly} />
    
    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Expected return time?</h1>
        <FloatingTime label="Expected Return Time" placeholder="eg: 14:00"  bind:value={form.outing_return_time}  {readOnly} />
    
    

{/if}


{#if form.contact_method=="outing_without_staff"}

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Outing without staff</h1>

    
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Where are they going?</h1>
    <textarea placeholder="eg: Skatepark" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.outing_destination}  {readOnly} ></textarea>
    

    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Expected return time?</h1>
        <FloatingTime label="Expected Return Time" placeholder="eg: 14:00"  bind:value={form.outing_return_time}  {readOnly} />
    

    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Have they signed the participant sign out sheet?</h1>
        <RadioButtonGroup options={yes_no_options} bind:value={form.participant_signed_out}  {readOnly} />
    


{/if}



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Any further comments?</h1>
    <textarea placeholder="write note here" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.additional_info}  {readOnly} ></textarea>

