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
        report_type: "EndOfShiftCJ",
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

<!-- <StaffTimeDate bind:value={form} /> -->

<Container>
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
</Container>

<!--  -->

<!--  -->
<Container>
    <h1 class="text-2xl mt-0 mb-2 drop-shadow mb-2">Routine RP's</h1>

    <Container>
        <h1 class="mt-0 mb-2">
            Did the fridge upstairs stock most of CJ's food with the fridge
            downstairs holding fruit and snacks for CJ at all times?
        </h1>
        <RadioButtonGroup
            options={yes_no_options}
            bind:value={form.fridges_stocked}
            {readOnly}
        />
        {#if form.fridges_stocked == "no"}
            <p class="mt-2">Provide details why this did not happen.</p>
            <textarea
                placeholder="details here..."
                class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
                style="height:70px"
                bind:value={form.fridges_not_stocked_details}
                {readOnly}
            ></textarea>
        {/if}
    </Container>

    <Container>
        <h1 class="mt-0 mb-2">
            All external gates have been locked at all times?
        </h1>
        <RadioButtonGroup
            options={yes_no_options}
            bind:value={form.external_gates_locked}
            {readOnly}
        />
        {#if form.external_gates_locked == "no"}
            <p class="mt-2">Provide details why this did not happen.</p>
            <textarea
                placeholder="details here..."
                class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
                style="height:70px"
                bind:value={form.external_gates_not_locked_details}
                {readOnly}
            ></textarea>
        {/if}
    </Container>

    <Container>
        <h1 class="mt-0 mb-2">
            CJ has 2:1 continuous accompaniment, understanding that there are
            always 2 people on with CJ in regards to his care inside the home?
        </h1>
        <RadioButtonGroup
            options={yes_no_options}
            bind:value={form.two_staff_members}
            {readOnly}
        />
        {#if form.two_staff_members == "no"}
            <p class="mt-2">Provide details why this did not happen.</p>
            <textarea
                placeholder="details here..."
                class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
                style="height:70px"
                bind:value={form.two_staff_members_details}
                {readOnly}
            ></textarea>
        {/if}
    </Container>

    <Container>
        <h1 class="mt-0 mb-2">
            The windows have remained locked either in an open state or closed
            state?
        </h1>
        <RadioButtonGroup
            options={yes_no_options}
            bind:value={form.windows_locked}
            {readOnly}
        />
        {#if form.windows_locked == "no"}
            <p class="mt-2">Provide details why this did not happen.</p>
            <textarea
                placeholder="details here..."
                class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
                style="height:70px"
                bind:value={form.windows_not_locked_details}
                {readOnly}
            ></textarea>
        {/if}
    </Container>

    <Container>
        <h1 class="mt-0 mb-2">
            CJ has restricted access to sharps and dangerous items. This
            includes the following: All sharps, including knives, scissors,
            metal cutlery. Dangerous objects that can be used as weapons such as
            (ceramic) plates and cups, and heavy countertop items?
        </h1>
        <RadioButtonGroup
            options={yes_no_options}
            bind:value={form.dangerous_items_restricted}
            {readOnly}
        />
        {#if form.dangerous_items_restricted == "no"}
            <p class="mt-2">Provide details why this did not happen.</p>
            <textarea
                placeholder="details here..."
                class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
                style="height:70px"
                bind:value={form.dangerous_items_not_restricted_details}
                {readOnly}
            ></textarea>
        {/if}
    </Container>

    <Container>
        <h1 class="mt-0 mb-2">
            CJ clothes have remained upstairs locked away for CJ's free access?
        </h1>
        <RadioButtonGroup
            options={yes_no_options}
            bind:value={form.clothes}
            {readOnly}
        />
        {#if form.clothes == "no"}
            <p class="mt-2">Provide details why this did not happen.</p>
            <textarea
                placeholder="details here..."
                class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
                style="height:70px"
                bind:value={form.clothes_details}
                {readOnly}
            ></textarea>
        {/if}
    </Container>
</Container>

<Container>
    <h1 class="text-2xl mt-0 mb-2 drop-shadow mb-2">PRN RP's</h1>

    <Container>
        <h1 class="mt-0 mb-2">
            DID you use a 1 or 2 person restraint at all with CJ?
        </h1>
        <RadioButtonGroup
            options={yes_no_options}
            bind:value={form.prn}
            {readOnly}
        />

        {#if form.prn == "yes"}
            <p class="mt-2">
                Provide details on the restraint used and why it was used.
            </p>
            <textarea
                placeholder="details here..."
                class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
                style="height:70px"
                bind:value={form.prn_details}
                {readOnly}
            ></textarea>
        {/if}
    </Container>

    <Container>
        <h1 class="mt-0 mb-2">
            Did you use a 1 or 2 person Escort, (that means when you did the
            restraint you then forced him to move to a certain location) at all
            with CJ?
        </h1>
        <RadioButtonGroup
            options={yes_no_options}
            bind:value={form.escorted_prn}
            {readOnly}
        />
        {#if form.escorted_prn == "yes"}
            <p class="mt-2">
                Provide details on the escord and why it was used.
            </p>
            <textarea
                placeholder="details here..."
                class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
                style="height:70px"
                bind:value={form.prn_escort_details}
                {readOnly}
            ></textarea>
        {/if}
    </Container>

    <!-- If any of these are yes, we should prompt the staff member to confirm they used them, and then ask why.  Ben also needs to be automatically emailed saying why the ysed it and a a summary emailed to him.  A report should be able to be run at the end of the month for how many times we ticked yes to any of these.-->
</Container>

<Container>
    <h1 class="text-1xl mb-2">Any other information?</h1>
    <textarea
        placeholder="other information..."
        class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
        style="height:70px"
        bind:value={form.additional_details}
        {readOnly}
    ></textarea>
</Container>

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
