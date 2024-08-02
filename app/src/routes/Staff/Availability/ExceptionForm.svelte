<script>
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";

    export let event={};
    event.recurring="once"
    event.all_day=true

    const date = new Date();
    
    let min_date = date.toLocaleString().slice(0, 10).split('/').reverse().join('-');

</script>

    <div class="mb-2">
        <div>Monday  4pm - 6pm</div>
        <div class="text-xs font-light">Volunteering <span class="text-gray-200">●</span> 24 Jul 2023 - 24 Jul 2024  </div>
    </div>
    <FloatingDate bind:value={event.available_date} label="Availabile Date" placeholder="eg: 30 Sep 2022" min={min_date} step="7" />

