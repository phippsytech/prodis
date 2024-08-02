<script>
    import KpiViewCaseNote from './KPIViewCaseNote.svelte';
    import {slide, fade} from 'svelte/transition';
    import {quintOut} from 'svelte/easing';
    import {formatDate, convertToHoursAndMinutes, convertDecimalHoursToHoursAndMinutes} from "@shared/utilities.js"

    export let line_items

    let selected_line_item_id = null;

    let claim_types = [
        {option:"Direct Service", value: null},
        {option:"Cancellation", value: "CANC"},
        {option:"NDIA Required Report", value: "REPW"},
        {option:"Provider Travel", value: "TRAN"},
        {option:"Non-Face-to-Face Services", value: "NF2F"},
    ];       
    
    function getOption(value){
        if (value =="") value=null;
        let option = "";
        claim_types.forEach((item)=>{
            if(item.value == value){
                option = item.option;
            }
        })
        return option;
    }

    function select(id){
        if (selected_line_item_id == id){
            selected_line_item_id = null;
            return;
        }
        selected_line_item_id = id;
    }

</script>

<div in:fade={{ delay:250, duration: 250,easing:quintOut }} out:fade={{duration:50,easing:quintOut}} class="hidden sm:block ">
    <div class="grid grid-cols-2 items-center py-1 text-xs font-medium text-gray-500" style="grid-template-columns: 6fr 1fr;">
        <div class="grid grid-cols-3 gap-4 items-center">
            <div>Date</div>
            <div>Client</div>
            <div>Service <span class="text-xs">(Claim Type)</span></div>
        </div>
        <div class="text-right">Time</div>
    </div>
</div>

<ul class="border-y border-gray-200 w-full mb-4">
    
    {#each line_items as item, index (item.id)}
        <li on:click={()=>select(item.id)} in:slide|global="{{duration: 250 }}" out:slide|global="{{duration: 250 }}" class="grid grid-cols-2 text-sm border-b border-gray-200 items-center hover:bg-indigo-50 py-1 cursor-pointer" style="grid-template-columns: 6fr 1fr; ">
            <div class="grid grid-cols-1 gap-0 sm:gap-4 sm:grid-cols-3 w-full items-center" >
                <div class="font-medium">{formatDate(item.session_date)}</div>
                <div class="whitespace-no-wrap">{item.client_name}</div>
                <div class=""> {item.service_code} <span class="text-xs">({getOption(item.claim_type)})</div>
            </div>
            <div class="text-right">{@html convertDecimalHoursToHoursAndMinutes(item.session_duration/60)}</div>
        </li>

        {#if selected_line_item_id==item.id}
        
        <KpiViewCaseNote line_item={item} />
        {/if}

    {/each}
    
</ul>
    