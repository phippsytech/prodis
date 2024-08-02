<script>

    
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    

    export let leave={};

</script>


        <div class="p-2 mb-2">
            <RadioButtonGroup columns={2} options={[
                
                {option:"Annual Leave", value: "annual"},
            {option:"Personal/Carer's Leave", value: "personal"},
            {option:"Unpaid Leave", value: "unpaid"}
            ]} bind:value={leave.leave_type} />

            <p class="mt-2 px-2 text-xs text-gray-700">Note: If there is insufficient leave balance at the time of processing, the remaining portion of leave will automatically be converted to unpaid leave.  </p>

        </div>


        <FloatingInput label="Reason for leave" bind:value={leave.reason} placeholder="eg: vacation, study, sick" />


        <div class="p-2 mb-2">
            <RadioButtonGroup columns={2}  options={[
                
                {value: "range", option: 'Date Range'},
                {value: "day", option: 'Whole Day'},
                {value: "part_day", option: 'Part of Day'},
            
            ]} bind:value={leave.date_type} />

        </div>

        <div class="flex flex-row justify-between gap-2  items-center">
            <div class="flex-grow">
                <div class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2">
                    <label class="text-xs opacity-50 p-0 m-0 block">Start Date</label>
                    <input type="date" class="w-full p-0  m-0 outline-none bg-white" bind:value={leave.start_date} />
                </div>
            </div>

            {#if leave.date_type == "range"}
            <div class="flex-grow">
                <div class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2">
                    <label class="text-xs opacity-50 p-0 m-0 block">End Date</label>
                    <input type="date" class="w-full p-0  m-0 outline-none bg-white" bind:value={leave.end_date} />
                </div>
            </div>
            {/if}
            
        </div>

        {#if leave.date_type == "range"}

        <p class=" px-2 text-xs text-gray-700 mb-4 ">Split the leave request into separate date ranges that exclude public holidays and weekends if they fall within the selected dates.  If part time, only enter dates for days you would normally work.</p>
        {/if}
        
        <FloatingInput label="Hours" type="number" bind:value={leave.hours} placeholder="eg: 7.6" autofocus />
        
