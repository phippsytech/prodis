<script>

    // based on https://docs.google.com/forms/d/e/1FAIpQLSeBA69-VytllpiF36QrHbatdVEsuN7GNA3fK3cBelMXim7TsQ/viewform

    
    import Container from '@shared/Container.svelte';
    
    import ReportPanel from '@shared/ReportPanel.svelte'
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDateSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte';
    import FloatingTime from '@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte';
    import RadioGroup from '@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte';
    import Ranking from '@shared/PhippsyTech/svelte-ui/Ranking.svelte';
    import StaffTimeDate from '../StaffTimeDate.svelte';

    const date = new Date();

    export let readOnly=false;
    export let staff_id= null;
    export let participant_id= null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "SleepTime",
        time_in_bed: null,
        time_asleep: null,
        additional_details: null
    }
    
</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4" style="color:#220055;">Sleep Time</div>

<StaffTimeDate bind:value={form}  {readOnly} />
<FloatingTime label="Time participant went to their room." bind:value={form.time_in_bed}  {readOnly} />
<FloatingTime label="Time you can confirm the participant was asleep" bind:value={form.time_asleep} {readOnly} />
<FloatingTextArea label="Any other info" bind:value={form.additional_details} {readOnly} />
