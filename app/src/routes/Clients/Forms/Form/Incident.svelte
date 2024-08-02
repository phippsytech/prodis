<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSc6BgFAvi2E-YCRhlWsPEYcrlfLTM40jkUCJVynhI9fJhBYWg/formResponse

    import Container from '@shared/Container.svelte';
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';;
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    import RadioGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte';
    import RadioButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte';
    import CheckboxButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    import HouseStaffCheckboxGroup from '../HouseStaffCheckboxGroup.svelte';

    import ClientStaffCheckboxGroup from '../ClientStaffCheckboxGroup.svelte';
    
    
    const date = new Date();

    export let readOnly = false;
    export let staff_id= null;
    export let participant_id= null;

    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "Incident",
        event_details: null,
        staff_present: [staff_id],
        staff_activity_prior: null,
        participant_activity_prior: null,
        staff_actions: null,
        event_duration: null,
        staff_activity_after: null,
        participant_activity_after: null,
        impact_ranking: null,
        report_required: null,   
        incident_types: [
            {
                value: false,
                name: "Property Damage",
                details: null,
            },
            {
                value: false,
                name: "First Aid required for staff",
                details: null,
            },
            {
                value: false,
                name: "First Aid required for participant",
                details: null,
            },
            {
                value: false,
                name: "Physical assualt to staff",
                details: null,
            },
            {
                value: false,
                name: "Physical assualt to public",
                details: null,
            },
            {
                value: false,
                name: "Administration of Chemical PRN",
                details: null,
            },
            {
                value: false,
                name: "Physical Restraint Used",
                details: null,
            },
            {
                value: false,
                name: "Enivronmental Restraint Used",
                details: null,
            },
            {
                value: false,
                name: "Seculsion Used",
                details: null,
            },
            {
                value: false,
                name: "Mechanical Restraint Used",
                details: null,
            },
            {
                value: false,
                name: "Missed Medication",
                details: null,
            }
        ],
        management_notified: null,
        property_damage: null,
        emergency_services: null,
        additional_details: null,
        resolved: null
    }
 
    const yes_no_options = [
        {value: 'yes', option: 'Yes'},
        {value: 'no', option: 'No'},
    ]

// ABC = Antcetal / Behavior / Consequence
    // what happened before the behaviour?
    // what happened during the behaviour?
    // what happened after the behaviour?


// what type of behaviour are you reporting?

    // positive
    // neutral
    // challenging



    
</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">ABC / Incident</div>

<div class="text-sm leading-6 text-gray-700">

<StaffTimeDate bind:value={form}  {readOnly} />
    
<!-- ABC SECTION -->

<Container>
<Container>
<div class="font-bold mb-2" >Who is working the shift during the incident?</div>
<HouseStaffCheckboxGroup bind:values={form.staff_present} bind:client_id={form.participant_id} {readOnly} />
</Container>    








<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What behaviour are you reporting?</h1>
    <textarea placeholder="event details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.event_details}  {readOnly} ></textarea>
</Container>

<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">What were you/staff doing prior to the participant having a behaviour?</h1>
    <p class="text-sm mb-2">If there is more than 1 staff, please note who was doing what.</p>
    <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.staff_activity_prior}  {readOnly} ></textarea>
</Container>

<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What was the particpant doing before the event?</h1>
    <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.participant_activity_prior}  {readOnly} ></textarea>
</Container>

<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">During the behaviour what did you/staff do?</h1>
    <p class="text-sm mb-2">If there is more than 1 staff, please note who was doing what.</p>
    <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.staff_actions}   {readOnly} ></textarea>
</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">How long did the event last?</h1>
    <textarea placeholder="eg: 2hrs 30mins" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.event_duration}  {readOnly} ></textarea>
</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What did the participant do after the event?</h1>
    <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.participant_activity_after}  {readOnly} ></textarea>
</Container>


<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">What do you believe de-escalated the event?</h1>
    <p class="text-sm mb-2">If there is more than 1 staff, please note who was doing what.</p>
    <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.staff_activity_after}  {readOnly} ></textarea>
</Container>

<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">What was the intensity of the event?</h1>
    <p class="text-sm mb-2">
        1: Minimal to no impact.
        10: Major impact to participant - Emergency services required.
    </p>
    <Ranking bind:rank={form.impact_ranking}  {readOnly} />

</Container>

<Container>
    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">Does this event require an Incident report?</h1>
    <p class="text-sm mb-2">
        If there was Violence, Aggression, Property Damage, PRN use, RP use, or anything else that is more that just a simple behaviour then the incident report MUST be completed.</p>
    <RadioButtonGroup options={yes_no_options} bind:value={form.report_required}  {readOnly} />
</Container>
</Container>

<!-- END ABC SECTION -->


<!-- INCIDENT SECTION -->
<Container>

    <h1 class="text-black text-2xl font-bold mt-0 drop-shadow mb-0">Incident Report</h1>
    <p class="text-sm ">This is more than an ABC, something that requires documentation or reporting.<br/>If you administered any PRN or used any restrictive practice this section <b>must</b> be filled out.</p>
    
    
<Container>
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">Why are you filling out this incident?</h1>
        <p class="text-sm mb-2">For all checked boxes, Please explain why were the action was necessary in the field provided.  If you gave medications note the name and dose.  If you did a physical restraint note the time done.</p>
        <div class=" space-y-1">
            {#each form.incident_types as incident_type}
                <div class="relative flex gap-x-3">
                    <div class="flex h-6 items-center">
                        <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" bind:checked={incident_type.value} id={incident_type.name} name={incident_type.name}  {readOnly} >
                    </div>
                    <div class=" w-full">
                        <label for={incident_type.name} class="font-medium text-gray-900">{incident_type.name}</label>
                        {#if incident_type.value}
                            <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px"  {readOnly}  ></textarea>
                        {/if}
                    </div>
                </div>
            {/each}
        </div>
    </Container>

<Container>
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Was there property damage?</h1>
    <!-- The form has an upload button for a file.  I am not sure what the intention of this is.  -->
    <p>(File upload)</p>
    
</Container>
  

<Container>
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Have you notified management?</h1>
        <RadioButtonGroup options={yes_no_options} bind:value={form.management_notified}  {readOnly} />
    
    </Container>
    

<Container> 
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">Were emergency services required?  IF so please document the Job number.</h1>
        <p class="text-sm mb-2">Write NA if not required.</p>
        <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.emergency_services}  {readOnly} ></textarea>
    </Container>   

<Container>
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-0">Any further information required?</h1>
        <p class="text-sm mb-2">Write NA if not required.</p>
        <textarea placeholder="provide details" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.additional_details}  {readOnly} ></textarea>
    </Container>   

<Container>
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Is the participant now safe and has the event deescalated?</h1>
        <RadioButtonGroup options={yes_no_options} bind:value={form.resolved}  {readOnly} />
    </Container>   
    
    </Container>
<!-- END INCIDENT SECTION -->



</div>
