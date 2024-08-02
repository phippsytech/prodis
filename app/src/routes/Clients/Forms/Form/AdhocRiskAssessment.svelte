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
    
    import MultipleChoiceGrid from '@shared/GForms/MultipleChoiceGrid.svelte';
    import DecisionMatrix from '@shared/GForms/DecisionMatrix.svelte';

    import TickBoxGrid from '@shared/GForms/TickBoxGrid.svelte';



    
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
        report_type: "AdhocRiskAssessment",
        participant_risk_matrix: [],
        staff_risk_matrix: [],

    }


    
 
</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Adhoc Risk Assessment</div>


<StaffTimeDate bind:value={form} {readOnly} /> 



<Container>

    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Is the primary stakeholder aware of the activity</h1>
    <RadioButtonGroup options={[
        {value: 'yes', option: 'Yes - they suggested it'},
        {value: 'yes_sms', option: 'Yes - We asked them via an SMS'},
        {value: 'no_sms', option: 'No - We tried via SMS but no response'},
        {value: 'no', option: "No we haven't asked them"}
    ]} bind:value={form.primary_stakeholder_aware} {readOnly} />
</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Do you have the go bag on you?</h1>
    <RadioButtonGroup options={yes_no_options} bind:value={form.go_bag} {readOnly} />
</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What is the activity?</h1>
        
        <textarea placeholder="about the activity..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.what_activity} {readOnly} ></textarea>
    </Container>

<Container>
        <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Where is the activity?</h1>
        
        <textarea placeholder="location of the activity..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.activity_location} {readOnly} ></textarea>
    </Container>


    <!-- <HouseStaffCheckboxGroup bind:values={form.staff_present} bind:client_id={form.participant_id} {readOnly} /> -->


    <!-- <CheckboxGroup options={staffList} bind:values={form.staff_present} /> -->




<Container>
    <h1 class="text-black text-2xl font-bold mt-0 mb-2 drop-shadow mb-2">What is the risk to the participant?</h1>
<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">How likely is it that this activity will result in:</h1>
    <DecisionMatrix 
    rows = {[
        "Fatalities, ill health or permanent disability",
        "Serious injury or long-term illness",
        "Medical treatment and several days off work",
        "Minor injury, illness or damage",
    ]}
    columns = {[
        "Almost certain",
        "Very likely",
        "Likely",
        "Unlikely",
        "Very unlikely",
        "Extremely unlikely",
    ]}
    scoreLabels={[
        "Low",
        "Medium",
        "High",
        "Extreme"
    ]}
    scores={[
        [3, 3, 3, 2, 1, 0],
        [3, 3, 2, 1, 1, 0],
        [2, 2, 1, 1, 0, 0],
        [1, 1, 1, 0, 0, 0],
        
    ]}
    bind:score={form.participant_risk_score}
    bind:values={form.participant_risk_matrix}
/>
{#if form.participant_risk_score}
<div class="">The risk is {form.participant_risk_score}.</div>
{:else}
<div class="">0</div>
{/if}

    </Container>



        <Container>
        <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Can you do something to lessen the risk to the participant?</h1>
        <p>Describe what you will do to mitigate the risks.  This is not a 1 sentence answer, you should clearly define what you are going to do as a team to ensure the participant stays safe.</p>
        <textarea placeholder="details..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.can_you_lessen_participant_risk} {readOnly} ></textarea>

</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">After mitigation, how likely is it that this activity will result in:</h1>
    <DecisionMatrix 
    rows = {[
        "Fatalities, ill health or permanent disability",
        "Serious injury or long-term illness",
        "Medical treatment and several days off work",
        "Minor injury, illness or damage",
    ]}
    columns = {[
        "Almost certain",
        "Very likely",
        "Likely",
        "Unlikely",
        "Very unlikely",
        "Extremely unlikely",
    ]}
    scoreLabels={[
        "Low",
        "Medium",
        "High",
        "Extreme"
    ]}
    scores={[
        [3, 3, 3, 2, 1, 0],
        [3, 3, 2, 1, 1, 0],
        [2, 2, 1, 1, 0, 0],
        [1, 1, 1, 0, 0, 0],
        
    ]}
    bind:score={form.participant_risk_score}
    bind:values={form.participant_risk_matrix}
/>
{#if form.participant_risk_score}
<div class="">The mitigated risk is {form.participant_risk_score}.</div>
{:else}
<div class="">0</div>
{/if}

    </Container>


</Container>




<Container>
    <h1 class="text-black text-2xl font-bold mt-0 mb-2 drop-shadow mb-2">What is the risk to Staff?</h1>
<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">How likely is it that this activity will result in:</h1>
    <DecisionMatrix 
    rows = {[
        "Fatalities, ill health or permanent disability",
        "Serious injury or long-term illness",
        "Medical treatment and several days off work",
        "Minor injury, illness or damage",
    ]}
    columns = {[
        "Almost certain",
        "Very likely",
        "Likely",
        "Unlikely",
        "Very unlikely",
        "Extremely unlikely",
    ]}
    scoreLabels={[
        "Low",
        "Medium",
        "High",
        "Extreme"
    ]}
    scores={[
        [3, 3, 3, 2, 1, 0],
        [3, 3, 2, 1, 1, 0],
        [2, 2, 1, 1, 0, 0],
        [1, 1, 1, 0, 0, 0],
        
    ]}
    bind:score={form.staff_risk_score}
    bind:values={form.staff_risk_matrix}
/>
{#if form.staff_risk_score}
<div class="">The risk is {form.staff_risk_score}.</div>
{:else}
<div class="">0</div>
{/if}

    </Container>



        <Container>
        <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Describe what you will do in detail to mitigate the risk to make it safer for staff.</h1>
        
        <textarea placeholder="details..." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.can_you_lessen_staff_risk} {readOnly} ></textarea>

</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">After mitigation, how likely is it that this activity will result in:</h1>
    <DecisionMatrix 
    rows = {[
        "Fatalities, ill health or permanent disability",
        "Serious injury or long-term illness",
        "Medical treatment and several days off work",
        "Minor injury, illness or damage",
    ]}
    columns = {[
        "Almost certain",
        "Very likely",
        "Likely",
        "Unlikely",
        "Very unlikely",
        "Extremely unlikely",
    ]}
    scoreLabels={[
        "Low",
        "Medium",
        "High",
        "Extreme"
    ]}
    scores={[
        [3, 3, 3, 2, 1, 0],
        [3, 3, 2, 1, 1, 0],
        [2, 2, 1, 1, 0, 0],
        [1, 1, 1, 0, 0, 0],
        
    ]}
    bind:score={form.staff_mitigated_risk_score}
    bind:values={form.staff_mitigated_risk_matrix}
/>
{#if form.staff_mitigated_risk_score}
<div class="">The mitigated risk is {form.staff_mitigated_risk_score}.</div>
{:else}
<div class="">0</div>
{/if}

    </Container>


</Container>




        <Container>
    
            <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Final steps on what to do...</h1>
    
            
    
            <RadioButtonGroup options={[
                {value: 'low', option: 'Low - the participant or staff - Complete the activity'},
                {value: 'medium', option: 'Medium - the participant / Staff - Complete the actiivty as long as the primary stakeholder knows and there is a message in the discord channel saying what your doing before you do it.'},
                {value: 'high_to_participant', option: 'High to the participant - Check that the primary stakeholder is ok with you doing the activity with him and confirmed in writting.'},
                {value: 'high_to_staff', option: 'High to staff -  Check with House lead or management and confirm its ok'},
                {value: 'extreme', option: "EXTREME - the participant or staff -  Defer activity , Notify Houseleaders/ Management and get them to speak to the primary stakeholder and have it ok'd in writing"}
            ]} bind:value={form.final_risk_assessment} {readOnly} />
        
    
            </Container>


<Container>

    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Who have you spoken to from the management team?</h1>

    <textarea placeholder="Enter the names of management team staff you have notified." class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.who_have_you_notified} {readOnly} ></textarea>

</Container>
