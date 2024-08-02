<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSeB4svEiR21ZnfAB0-E8MssuNrbWpItzCRDB4jAXYkXOWO4cw/viewform

    import Container from '@shared/Container.svelte';
    import Breadcrumbs from '@shared/Breadcrumbs.svelte';
    import Footer from '@shared/Footer.svelte';
    import ReportPanel from '@shared/ReportPanel.svelte'
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    import RadioGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';
    import Camera from '@shared/PhippsyTech/svelte-ui/Camera.svelte';
    
    const date = new Date();
    
    export let  readOnly=false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        house_id: participant_id,
        report_type: "Maintenance",
        issue: "What is the issue in the house?",
        cause: "how did it happen?",
        urgent: "no",
        photo_sent: "no",
        additional_details: "Additional details to add"
    }

    let picture;

    
</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Maintenance Request</div>

<StaffTimeDate bind:value={form}  {readOnly} />


    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What house is the request for</h1>
    <p>
        (this could be automated)<br/>
        Logan<br/>
        CJ<br/>
        Rhys<br/>
    </p>    



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What is the issue in the house?</h1>
    <p>(text box)</p>



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Do you know how it happened?</h1>
    <p>This is not to get someone in trouble but to engineer it out of happening again if possible.</p>
    <p>(text box)</p>



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Is this urgent requiring a 24 hour fix?</h1>
    <p>
        (options)<br/>
        No<br/>
        Yes - I will follow this up with a group message on discord and an sms to my house lead <br />
        <!-- (could this be automated in future?) -->
    </p>



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Have you sent a photo to the maintenance channel in discord?</h1>
    {#if picture}
        <img src={picture} />
    {:else}
    <Camera bind:value={picture}  {readOnly} />
    {/if}
    <p>
        (options)<br/>
        Yes<br/>
        No<br />
        <!-- (could this be automated in future?) -->
    </p>



    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Any other information?</h1>
    <p>(text box)</p>
