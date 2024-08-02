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
    import CheckboxGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxGroup.svelte';
    import CheckboxButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    
    const date = new Date();

    const yes_no_options = [
        {value: 'yes', option: 'Yes - please sms House Lead'},
        {value: 'no', option: 'No'},
    ]

    let ingestion_options=[
        {value: 'fluid', option: 'Fluid'},
        {value: 'food', option: 'Food'},
    ]

    const food_options=[
        {value: 'meal', option: 'Meal'},
        {value: 'snack', option: 'Snack'},
        {value: 'mother', option: 'Food eaten while in mums care'},
        
    ]

    export let readOnly= false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "FoodIntake",
        ingesting: [],
        fluid_type: null,
        fluid_volume: null,
        food:null,
        food_type:null,
        food_volume:null,
        
        hazard: null,
    }
    
    
</script>
<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Food &amp; Intake</div>

<StaffTimeDate bind:value={form}  {readOnly} />


    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What is the participant ingesting?</h1>
    <CheckboxButtonGroup options={ingestion_options} bind:values={form.ingesting}  {readOnly} />


{#if form.ingesting.includes('fluid')}

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What type of fluid was it</h1>
    


    <RadioButtonGroup options={[
        {value: 'softdrink', option: 'Softdrink'},
        {value: 'juice', option: 'Juice'},
        {value: 'water', option: 'Water'},
        {value: 'milk', option: 'Milk'},
        {value: 'hot_drink', option: 'Hot Drink'}
    ]} bind:value={form.fluid_type}  {readOnly} />




    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">How much fluid was consumed?</h1>

    <RadioButtonGroup options={[
        {value: 'sip', option: 'Sip (50mls)'},
        {value: 'small', option: 'Small Cup (150mls)'},
        {value: 'large', option: 'Large Cup (300mls)'},
        {value: 'bottle', option: '600ml bottle'},
        
    ]} bind:value={form.fluid_volume}  {readOnly} />



{/if}

{#if form.ingesting.includes('food')}

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Is this a main meal or a snack?</h1>
    <RadioButtonGroup options={food_options} bind:value={form.food}  {readOnly} />



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">What type of food was it?</h1>
    <textarea placeholder="write note here" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.food_type}  {readOnly} ></textarea>



    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">How much food was eaten?</h1>
    <textarea placeholder="write note here" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.food_volume}  {readOnly} ></textarea>




{/if}

{#if form.ingesting.length>0}

    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Was there any abnormal coughing or choking?</h1>
    <RadioButtonGroup options={[
        {value: 'yes', option: 'Yes'},
        {value: 'no', option: 'No'},
    ]} bind:value={form.hazard}  {readOnly} />


{#if form.hazard=="yes"}
    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 mt-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-yellow-700">
                Please SMS House Lead
              
            </p>
          </div>
        </div>
      </div>
{/if}




{/if}


    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Additional Info?</h1>
    <textarea placeholder="write note here" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.additional_info}  {readOnly} ></textarea>


