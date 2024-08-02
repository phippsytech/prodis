<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform
    import {push} from 'svelte-spa-router'
    import Container from '@shared/Container.svelte';
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    import Camera from '@shared/PhippsyTech/svelte-ui/Camera.svelte';
    import { ReceiptRefundIcon } from 'heroicons-svelte/24/outline';
    import { crossfade } from 'svelte/transition';
    
    const date = new Date();

    export let readOnly = false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "Expense",
        photo: null
    }

    // this is for team leads only.

        // Take a photo of Receipt
        // tick is this a house credit card
        // or is this a reimbursement to themselves.

        // use a file upload rather than the camera thing because it doesn't work nicely

        
        
</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Expense</div>

<StaffTimeDate bind:value={form}  {readOnly}  />

{#if form.photo}
    <img src={form.photo} class="w-full" />
    <a on:click={()=>form.photo=null} class="text-blue-600">try again</a>
{:else}
    <Camera bind:value={form.photo}  {readOnly}  />
{/if}

<div class="font-medium mt-2 mb-1" >Details</div>

<textarea placeholder="Quick summary of this expense" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.details}  {readOnly} ></textarea>