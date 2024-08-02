<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLScT7fLGNXswoG06qsnsRUWf0Rxy-B-tARIcsMKhRqwbtgGmlw/formResponse

    
// Pre Activity Checklist

// Adhoc Risk Assessment (new to create - see email)


    import Container from '@shared/Container.svelte';
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    import RadioGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte';
    import CheckboxGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxGroup.svelte';
    import RadioButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte';
    import CheckboxButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    import HouseStaffCheckboxGroup from '../HouseStaffCheckboxGroup.svelte';
    
    
    const yes_no_options = [
        {value: 'yes', option: 'Yes'},
        {value: 'no', option: 'No'},
    ]
    const activity_options=[
        {value: 'school', option: 'School run'},
        {value: 'outing', option: 'Outing in the community'},
        {value: 'new task', option: 'New house task'},
        {value: 'staff safety', option: 'Staff safety in or out of the home'}
    ]
    const outing_options=[
        {value: 'water', option: 'Water'},
        {value: 'snacks', option: 'Snacks'},
        {value: 'meal', option: 'A full meal'},
        {value: 'toileting', option: 'Toileting supplies'},
        {value: 'clothes', option: 'Change of clothes'},
        {value: 'chews', option: 'Dummies / Chews'},
        {value: 'toys', option: 'Toys'},
        {value: 'ppes', option: 'PPES'},
        {value: 'medications', option: 'Medications'},
        {value: 'school stuff', option: 'School books / homework'},
        {value: 'towel', option: 'Towel'}, 
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
        report_type: "RiskAssessment",
        cancel_activity: null,
        staff_present: [staff_id],
        leave_time: null,
        expected_return_time: null,
        activity: null,
        school_run:{
            drive_time: null,
            car: null,
        },
        outing:[],
        anticipated_behaviour_plan: null   
    }



    
 
</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Pre-activity Checklist</div>


<StaffTimeDate bind:value={form} {readOnly} />

    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Have you read the BSP?  Is there any reasons why you should not complete the task / event today?</h1>
    <RadioButtonGroup options={yes_no_options} bind:value={form.cancel_activity} {readOnly} />



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Which staff are on shift?</h1>

    <HouseStaffCheckboxGroup bind:values={form.staff_present} bind:client_id={form.participant_id} {readOnly} />


    <!-- <CheckboxGroup options={staffList} bind:values={form.staff_present} /> -->



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What time are you leaving the house?</h1>
    <FloatingTime label = "What time are you leaving the house?" bind:value={form.leave_time} {readOnly} />



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What time do you expect to return to the house?</h1>
    <FloatingTime label = "What time do you expect to return to the house?" bind:value={form.expected_return_time} {readOnly} />



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What are you risk assessing?</h1>
    <RadioButtonGroup options={activity_options} bind:value={form.activity} {readOnly} />


{#if form.activity=="school"}
    
        <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Is this an AM or PM drive?</h1>
        <RadioButtonGroup options={[
            {value: 'am', option: 'AM'},
            {value: 'pm', option: 'PM'}
        ]} bind:value={form.school_run.drive_time} {readOnly} />
    

    
        <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Is this a company car or staff car?</h1>
        <RadioButtonGroup options={[
            {value: 'company', option: 'Company Car'},
            {value: 'personal', option: 'Staff personal vehicle'}
        ]} bind:value={form.school_run.car}  {readOnly}  />
    
{/if}

{#if form.activity=="outing"}
    
        <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Do you need to take any of the following items with you?</h1>
        <CheckboxButtonGroup options={outing_options} bind:values={form.outing} {readOnly} />
    

    
        <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Do you think there may be any behaviours while out in the community?</h1>
        <p>Verbal aggression, physicall aggresssion, absconding?  Give a brief description on what you will do if they happen while offsite.</p>
        <textarea placeholder="other information..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.anticipated_behaviour_plan} {readOnly} ></textarea>
    
{/if}
