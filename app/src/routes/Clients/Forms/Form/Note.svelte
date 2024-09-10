<script>
    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
    import FloatingTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte";
    import RadioGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioGroup.svelte";
    import Ranking from "@shared/PhippsyTech/svelte-ui/Ranking.svelte";
    import StaffTimeDate from "../StaffTimeDate.svelte";

    const date = new Date();

    export let staff_id = null;
    export let participant_id = null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, "0")}-${date.getDate().toString().padStart(2, "0")}`,
        time: date.toTimeString().slice(0, 5),
        participant_id: participant_id,
        staff_id: staff_id,
        form_data: {
            report_type: "Note",
            note: null,
        },
    };
    export let readOnly = false;
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4"
    style="color:#220055;"
>
    
</div>

<StaffTimeDate bind:value={form} {readOnly} />

<textarea
    placeholder="write note here"
    class="rounded border py-0 p-1.5 border-gray-300 focus:border-blue-600 focus:outline-none w-full"
    style="height:150px"
    bind:value={form.form_data.note}
    {readOnly}
    autofocus
></textarea>
