<script>
    import ReportTypeSelector from "@shared/ReportTypeSelector.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    export let props = {};
</script>

<ReportTypeSelector bind:report_type={props.report_type} />
<FloatingDate label="Report Due Date" bind:value={props.due_date} />
