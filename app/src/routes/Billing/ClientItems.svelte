<script>
    import HouseBilling from "./HouseBilling.svelte";
    import ExpandIndicator from "@app/Layout/ExpandIndicator.svelte";

    export let client_id=1

    let client_expanded=true;
    let planmanagers_expanded=true;

</script>


<div class="flex items-start items-center gap-x-2 py-1 {(client_expanded)?'':'border-b border-solid border-b-gray-300'} ">
    <input id="comments" aria-describedby="comments-description" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
    <label for="comments" class="text-base font-semibold text-gray-900 flex items-center justify-between w-full">Cristian Salisbury
        <span class="flex">$487.78
            <ExpandIndicator class="h-5 w-5 flex-none text-gray-400 mx-4" expanded={client_expanded} />
        <!-- <svg class="h-5 w-5 flex-none text-gray-400 mx-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" >
            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"
            />
        </svg> -->
    </span>
    </label>
</div>
{#if client_expanded}
<div class="bg-white border-y border-solid border-y-gray-400">
<div class="border-l-2 border-gray-200 border-dotted pl-0 ml-2">
<div class="flex items-start items-center gap-x-2 py-1 pl-2  bg-gray-200">
    <input id="comments" aria-describedby="comments-description" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
    <label for="comments" class="text-sm text-gray-800 flex items-center justify-between w-full">
        Plan Partners
        
        <span class="flex">$487.78
        <svg class="h-4 w-4 flex text-gray-400 mx-4 " fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path>
          </svg>
        </span>
    </label>
</div>

<HouseBilling house_id={client_id} start_date="2023-06-19" end_date="2023-06-25" />
</div>

</div>
{/if}

