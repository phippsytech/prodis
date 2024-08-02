<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSe3h0bn2VcPJTPcUADSQmnwyALBQ1i3qACV6jP102bldHKfiw/formResponse

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
    import CheckboxGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxGroup.svelte';
    import CheckboxButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte';
    import BristolStoolChart from '../BristolStoolChart.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';

    const yes_no_options = [
        {value: 'yes', option: 'Yes'},
        {value: 'no', option: 'No'},
    ]
    const date = new Date();

    export let readOnly = false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "Toilet",
        used_toilet: null,
        in_toilet: null,
        toilet_events: [],
        urination:{
            size: null,
            difficulty: null,
        },
        defecation:{
            stool_type: null, // 1-7 on the bristol stool chart
            size: null,
            difficulty: null,
        },
        additional_details: null
    }


    $:{
        if(form.in_toilet=="no"){
            form.used_toilet="yes"
        }
    }

</script>
<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Toilet Log</div>


<StaffTimeDate bind:value={form} {readOnly} />


    <h1 class="text-black text-1xl font-bold mt-0 mb-2">Was this event in a toilet?</h1>
    <RadioButtonGroup options={yes_no_options} bind:value={form.in_toilet} {readOnly} />


{#if form.in_toilet == 'yes'}

    <h1 class="text-black text-1xl font-bold mt-0 mb-2">Did the participant use the toilet on this occasion?</h1>
    <RadioButtonGroup options={[
        {value: 'yes', option: 'Yes'},
        {value: 'no', option: 'No'},
        {value: 'unsure', option: 'Unsure'},
    ]} bind:value={form.used_toilet} {readOnly} />

{/if}

{#if form.used_toilet == 'yes'}
    
        <h1 class="text-black text-1xl font-bold mt-0 mb-0">Which toileting events occurred?</h1>
        <CheckboxButtonGroup instructions="Select all that occurred" bind:values={form.toilet_events} options={[
            {value: 'urination', option: 'Urination (Number 1)'},
            {value: 'defecation', option: 'Defecation (Number 2)'}
        ]} {readOnly} />
    

    

    {#if form.toilet_events.includes('urination')}

        
            <h1 class="text-black text-1xl font-bold mt-0 mb-2 mb-2">Urination</h1>
            <h1 class="mt-0 mb-2">How would you describe this urination for the participant?</h1>
            <RadioButtonGroup options={[
                {value: 'small', option: 'Small'},
                {value: 'medium', option: 'Medium'},
                {value: 'large', option: 'Large'}
            ]} bind:value={form.urination.size} {readOnly} />
        
        
            <h1 class="mt-0 mb-2">Did the participant have any trouble Urinating?</h1>
            <RadioButtonGroup options={yes_no_options} bind:value={form.urination.difficulty} {readOnly} />
        
    {/if}

    {#if form.toilet_events.includes('defecation')}
        
            <h1 class="text-black text-1xl font-bold mt-0 mb-2 mb-2">Defecation</h1>
            
            <BristolStoolChart bind:rank={form.defecation.stool_type} {readOnly} />
            <h1 class="text-black text-1xl mt-4 mb-2">How would you describe the size of this bowel movement?</h1>
            <RadioButtonGroup options={[
                {value: 'small', option: 'Small'},
                {value: 'medium', option: 'Medium'},
                {value: 'large', option: 'Large'}
            ]} bind:value={form.defecation.size} {readOnly} />
        
        
            <h1 class="mt-0 mb-2">Did the participant have any trouble Defecating?</h1>
            <RadioButtonGroup options={yes_no_options} bind:value={form.defecation.difficulty} {readOnly} />
        
    {/if}
{/if}



    <!-- <h1 class="mt-0 mb-2">Any other information to add?</h1> -->

<FloatingTextArea label="Any other information to add?" bind:value={form.additional_details} placeholder="Additional information" {readOnly} ></FloatingTextArea>

    <!-- <textarea placeholder="additional information" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.additional_details} {readOnly} ></textarea> -->

