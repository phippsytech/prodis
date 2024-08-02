<script>
    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform

    import Container from "@shared/Container.svelte";
    import FloatingNumeric from "@shared/PhippsyTech/svelte-ui/forms/FloatingNumeric.svelte";
    import FloatingTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import StafferShiftTimeline from "@app/routes/Clients/Timeline/StafferShiftTimeline.svelte";

    const date = new Date();

    export let readOnly = false;
    export let staff_id = null;
    export let participant_id = null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, "0")}-${date.getDate().toString().padStart(2, "0")}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "EndOfShift",
        positive_engagements: null,
        negative_moments: null,
        additional_details: null,
    };

    const yes_no_options = [
        { value: "yes", option: "Yes" },
        { value: "no", option: "No" },
    ];
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4"
    style="color:#220055;"
>
    End of Shift Notes
</div>

<FloatingTime
    label="Shift Start Time"
    bind:value={form.start_shift}
    step="900"
    {readOnly}
/>
<FloatingTime
    label="Shift End Time"
    bind:value={form.end_shift}
    step="900"
    {readOnly}
/>
<!-- <p class="text-xs ml-1 opacity-50">NOTE: If end time is before start time, an overnight shift is assumed (eg: 22:00 - 09:00)</p> -->
<!-- <FloatingNumeric label="Kilometres Travelled" bind:value={form.kilometers_travelled} placeholder="eg: 7 (optional - leave blank for none)"    {readOnly} /> -->

<Container>
    <div class="text-2xl drop-shadow">Timeline Report</div>
    <p class="text-md text-gray-500 mb-2">
        Events <span class="font-medium italic underline">you</span> reported during
        the shift.
    </p>

    <!-- <p>The timestamp report would be in chronological order starting from the beginning of the shift.  It would only contain notes and forms recorded by this staff member</p> -->
    <StafferShiftTimeline
        client_id={form.participant_id}
        date={form.date}
        start_time={form.start_shift}
        end_time={form.end_shift}
        staff_id={form.staff_id}
    />
</Container>

<!-- 
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">What shift?</h1>

    I believe this can be automated by the start time for the staff member.

<p>(Radio buttons)
    AM<br/>
    PM<br/>
    Active<br/>
    Passive<br/>
</p>
 -->

<!-- <div class="font-medium mb-2 text-sm">Where there any Positive engagements this shift?  What were they?</div> -->

<!-- <h1 class="font-medium mt-2 mb-0">Were there any Positive engagements this shift?  What were they?</h1>
    <p class="text-sm text-gray-500 mb-1">Please write N/A if none</p>
    <textarea placeholder="Positive engagements this shift" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.positive_engagements}  {readOnly} ></textarea>



    <h1 class="font-medium mt-2 mb-0">Any Negative moments this shift? What were they?</h1>
    <p class="text-sm text-gray-500 mb-1">Please write N/A if none</p>
    <textarea placeholder="Negative engagements this shift" class="rounded border py-0 p-1.5 border-gray-300  focus:border-blue-600 focus:outline-none w-full" style="height:70px" bind:value={form.negative_moments}  {readOnly} ></textarea>
 -->

<h1 class="font-medium mt-2 mb-1">Any other information?</h1>
<textarea
    placeholder="other information..."
    class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
    style="height:70px"
    bind:value={form.additional_details}
    {readOnly}
></textarea>

<!-- <FormActions bind:form={form} /> -->

<!-- <div class="flex justify-end">
    <Button label="Save" on:click={()=>saveForm(form)} />
</div> -->
