<script>
    import { onMount } from "svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte";
    import { jspa } from "@shared/jspa.js";
    import RTE from "@shared/RTE/RTE.svelte";

    export let meeting_note = {};

    let staff = [];
    let staffList = [];
    let mounted = false;

    onMount(async () => {
        await jspa("/Staff", "listStaff", {}).then(
            (result) => (staff = result.result),
        );
        mounted = true;
    });

    $: {
        staff.forEach((staffer) => {
            if (staffer.archived != 1)
                staffList.push({ option: staffer.name, value: staffer.name });
        });
        staffList = staffList;
    }
</script>

{#if mounted}
    <FloatingInput
        bind:value={meeting_note.purpose}
        label="Meeting Purpose"
        placeholder="eg: Weekly Meeting"
    />
    <FloatingDate bind:value={meeting_note.meeting_date} label="Meeting Date" />
    <FloatingTime
        bind:value={meeting_note.start_time}
        mode="time"
        label="Start Time"
        placeholder="eg: 10:00"
    />
    <FloatingTime
        bind:value={meeting_note.end_time}
        mode="time"
        label="End Time"
        placeholder="eg: 13:00"
    />
    <FloatingSelect
        bind:value={meeting_note.supervisor}
        label="Supervisor"
        instruction="Select supervisor"
        options={staffList}
        hideValidation={true}
    />

    <FloatingTextArea
        bind:value={meeting_note.staff}
        label="Attending Staff"
        placeholder="Enter the names of all staff attending this meeting."
    />
    <FloatingInput
        bind:value={meeting_note.location}
        label="Location"
        placeholder="eg: 1 Someplace St, Adelaide SA 1234"
    />
    <!-- <FloatingTextArea bind:value={meeting_note.notes} label="Notes" placeholder="Enter meeting notes here." /> -->

    <div class="mb-2">
        <div class="text-xs opacity-50 px-3 my-2">Notes</div>
        <RTE bind:content={meeting_note.notes} />
    </div>
{/if}
