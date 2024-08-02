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
        {value: 'outing', option: 'Community Activity'},
        
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

    export let valid=true;
    export let readOnly = false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "NewRiskAssessment",
        bsp_read: null,
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


  function checkMinimumLength(str, length){
    if (str == null) return false;
    return str.length >= length;
  }
    

  function checkValidity(form){
    if (form.bsp_read=="yes"){
        valid = (checkMinimumLength(form.activity_details, 50) && checkMinimumLength(form.anticipated_behaviour_risks, 5) && checkMinimumLength(form.anticipated_behaviour_plan, 50))
    } else {
        valid = false;
    }
    return valid;
  }

  $: valid = checkValidity(form);
  

 
</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Pre-activity Checklist</div>


<StaffTimeDate bind:value={form} {readOnly} />

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-1">Have you read the BSP?</h1>
    <RadioButtonGroup options={yes_no_options} bind:value={form.bsp_read} {readOnly} />

{#if form.bsp_read == "yes"}

    <h1 class="text-black text-1xl font-bold mt-4 mb-2 drop-shadow">Which staff are on shift?</h1>

    <HouseStaffCheckboxGroup bind:values={form.staff_present} bind:client_id={form.participant_id} {readOnly} />


    <!-- <CheckboxGroup options={staffList} bind:values={form.staff_present} /> -->

    <h1 class="text-black text-1xl font-bold mt-4 mb-2 drop-shadow">What are you risk assessing?</h1>
    <RadioButtonGroup options={activity_options} bind:value={form.activity} {readOnly} />


    {#if form.activity =="outing"}
    <h1 class="text-black text-1xl font-bold mt-4 mb-2 drop-shadow">Have you you got approval for this activity?</h1>
    <RadioButtonGroup options={yes_no_options} bind:value={form.activity_approval} {readOnly} />
    
    {#if form.activity_approval=="yes"}
    <h1 class="text-black text-1xl font-bold mt-4 mb-2 drop-shadow">Who gave you approval for this activity?</h1>
    <textarea placeholder="Approved by (min 5 chars)" class="rounded border py-0 p-1.5 {checkMinimumLength(form.activity_approver, 5)?'border-gray-300 focus:border-blue-600':'border-red-600 focus:border-red-600'} focus:outline-none w-full" style="height:70px" bind:value={form.activity_approver} {readOnly} ></textarea>
    {/if}
    
    
    
    {/if}
    

{#if form.activity =="outing"}
    <h1 class="text-black text-1xl font-bold mt-4 mb-1 drop-shadow">What will you be doing in the community?</h1>
    <!-- [character limit - 50] -->
    <textarea placeholder="What will you be doing in the community? (min 50 chars)" class="rounded border py-0 p-1.5 {checkMinimumLength(form.activity_details, 50)?'border-gray-300 focus:border-blue-600':'border-red-600 focus:border-red-600'} focus:outline-none w-full" style="height:70px" bind:value={form.activity_details} {readOnly} ></textarea>
{/if}



    <h1 class="text-black text-1xl font-bold mt-4 mb-1 drop-shadow">What time will the activity commence?</h1>
    <FloatingTime label="Commences"  bind:value={form.leave_time} {readOnly} />



    <h1 class="text-black text-1xl font-bold mt-4 mb-1 drop-shadow">What time will the activity conclude?</h1>
    <FloatingTime label = "Concludes" bind:value={form.expected_return_time} {readOnly} />




    <h1 class="text-black text-1xl font-bold mt-4 mb-1 drop-shadow">How are you travelling?</h1>
    <RadioButtonGroup options={[
        {value: 'car', option: 'Car'},
        {value: 'walking', option: 'Walking'}
    ]} bind:value={form.travel_method} {readOnly} />
    
    
    {#if form.travel_method == "car"}
    
    
    
    <h1 class="text-black text-1xl font-bold mt-4 mb-2 drop-shadow">Is this a company car or staff car?</h1>
    <RadioButtonGroup options={[
        {value: 'company', option: 'Company Car'},
        {value: 'personal', option: 'Staff personal vehicle'}
    ]} bind:value={form.school_run.car}  {readOnly}  />
    
    {/if}
    






{#if form.school_run.drive_time == "pm"}


<RadioButtonGroup options={yes_no_options} bind:value={form.anticipated_behaviour_possible} {readOnly} />
{/if}





    
        <h1 class="text-black text-1xl font-bold mt-4 mb-2 drop-shadow">Do you need to take any of the following items with you?</h1>
        <CheckboxButtonGroup options={[
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
            {value: 'go_back', option: 'Go bag'}, 
        ]} bind:values={form.outing} {readOnly} />
    



<h1 class="text-black text-1xl font-bold mt-4  drop-shadow ">Is there a potential for any behaviours of concern during this activity?</h1>



<p class="text-sm mb-1">Verbal aggression, physical aggresssion, absconding?  Give a brief description on what you will do if they happen while offsite.</p>

<RadioButtonGroup options={yes_no_options} bind:value={form.anticipated_behaviour_possible} {readOnly} />

{#if form.anticipated_behaviour_possible == "yes"}

<h1 class="text-black text-1xl font-bold mt-4 mb-2 drop-shadow">Outline what are the potential risks</h1>
[character limit - 5 ]
<textarea placeholder="Potential risks (min 5 chars)" class="rounded border py-0 p-1.5 {checkMinimumLength(form.anticipated_behaviour_risks, 5)?'border-gray-300 focus:border-blue-600':'border-red-600 focus:border-red-600'} focus:outline-none w-full" style="height:70px" bind:value={form.anticipated_behaviour_risks} {readOnly} ></textarea>


<h1 class="text-black text-1xl font-bold mt-0  drop-shadow mt-4">Based on the above answer what are you going to do to mitigate those risks?</h1>
[character limit - 50]
<textarea placeholder="Mitigation Plan (min 50 chars)" class="rounded border py-0 p-1.5 {checkMinimumLength(form.anticipated_behaviour_plan, 50)?'border-gray-300 focus:border-blue-600':'border-red-600 focus:border-red-600'} focus:outline-none w-full" style="height:70px" bind:value={form.anticipated_behaviour_plan} {readOnly} ></textarea>



{/if}

<h1 class="text-black text-1xl font-bold mt-0  drop-shadow mt-4">Any other comments?</h1>

<textarea placeholder="other information..." class="rounded border py-0 p-1.5 focus:outline-none  w-full" style="height:70px" bind:value={form.additional_info} {readOnly} ></textarea>



{:else}

Read the BSP.  Once you have read the BSP you can click yes and continue with the checklist.

{/if}