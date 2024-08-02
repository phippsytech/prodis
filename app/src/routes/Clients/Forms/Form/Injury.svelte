<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform

    
    import Container from '@shared/Container.svelte';
    
    import Footer from '@shared/Footer.svelte';
    import ReportPanel from '@shared/ReportPanel.svelte'
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    import RadioGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte';
    import RadioButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte';
    import CheckboxGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxGroup.svelte';
    import CheckboxButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    
    
    const date = new Date();

    const yes_no_options = [
        {value: 'yes', option: 'Yes'},
        {value: 'no', option: 'No'},
    ]

    export let readOnly = false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "Injury",
        injury_cause: null,
        injury_areas: [],
        injury_types: [],
        injury_location: null,
        injury_severity: null,
        injury_treatment: null,
        first_aid_given: null,
        first_aid_details: null,
        escalation_required: null,        
        escalation_type: null,
        additional_info: null
    }
    
    

</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Injury</div>
<StaffTimeDate bind:value={form} {readOnly} />

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What was the participant doing to sustain the injuries?</h1>
    <textarea placeholder="write note here" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.injury_cause} {readOnly} ></textarea>



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What areas of the participant sustained injuries?</h1>

    <CheckboxButtonGroup options={[
        {value: 'head', option: 'Head'},
        {value: 'face', option: 'Face'},
        {value: 'upper_chest_front', option: 'Upper chest front'},
        {value: 'stomach', option: 'Stomach'},
        {value: 'back', option: 'Back'},
        {value: 'left_arm', option: 'Left arm'},
        {value: 'right_arm', option: 'Right arm'},
        {value: 'left_leg', option: 'Left leg'},
        {value: 'right_leg', option: 'Right leg'},
        {value: 'left_hand', option: 'Left hand'},
        {value: 'right_hand', option: 'Right hand'},
        {value: 'left_foot', option: 'Left foot'},
        {value: 'right_foot', option: 'Right foot'},
    ]} bind:values={form.injury_areas} {readOnly} />
    



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What types of injuries were sustained?</h1>

    <CheckboxButtonGroup options={[
        {value: 'grazes', option: 'Grazes'},
        {value: 'scratches', option: 'Scratches'},
        {value: 'cuts', option: 'Cuts'},
        {value: 'bruising', option: 'Bruising'},
        {value: 'burn', option: 'Burn'},
        {value: 'broken_bone', option: 'Broken bone'},
        {value: 'injested_non_food', option: 'Injested non-food item'},
        
    ]} bind:values={form.injury_types} {readOnly} />
    



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What is the severity of the injury?</h1>
    <p class="text-sm mb-2">
        1: Can't see anything wrong.  
        5: Hospital admission required
    </p>
<Ranking bind:rank={form.inury_severity} decimal=5 {readOnly} />




    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Was first aid given?</h1>

    <RadioButtonGroup options={yes_no_options} bind:value={form.first_aid_given} {readOnly} />
    


{#if form.first_aid_given=="yes"}

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">What first aid was performed?</h1>
    
    <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.first_aid_details} {readOnly} ></textarea>

{/if}



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Did staff escalate the injury to a professional / management?</h1>

    <RadioButtonGroup options={yes_no_options} bind:value={form.escalation_required} {readOnly} />

    {#if form.escalation_required=="yes"}
    
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Who did you escalate the injury to?</h1>
    
    <RadioButtonGroup options={[
        {value: 'doctor', option: 'Locum / GP'},
        {value: 'saas', option: 'SAAS - Ambulance'},
        {value: 'sapol', option: 'SAPOL'},
        {value: 'hospital', option: 'Hospital'},
        {value: 'house_lead', option: 'House Lead'},
        {value: 'manager', option: 'Manager'},
    ]} bind:value={form.escalation_type} {readOnly} />

{/if}





    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Additional Info</h1>
    <p>If you identified a risk, or if there is something we can change to prevent this in the future please let us know</p>
    <textarea placeholder="Any other information we need to know?" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.additional_info} {readOnly} ></textarea>

