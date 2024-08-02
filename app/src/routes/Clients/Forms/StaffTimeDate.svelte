<script>
    
    import FloatingDate from '@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';    
    import { formatDate } from '@shared/utilities.js';
    
    const date = new Date();
    const start_year = new Date().getFullYear() - 1
    const end_year = new Date().getFullYear() + 1

    export let value = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
    };
    export let labels = {
        date: "Date",
        time: "Time"
    };

    export let edit=false;

    export let readOnly = false;
    
</script>

{#if edit && !readOnly}
    <FloatingDate label={labels.date} bind:value={value.date} {start_year} {end_year} />
    <FloatingTime label={labels.time} bind:value={value.time} />
{:else}
    <div on:click={()=>{edit=true}} class="p-2 flex items-center text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
        <span>{value.time} - {formatDate(value.date)} </span>

        <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
        </svg>

    </div>
{/if}