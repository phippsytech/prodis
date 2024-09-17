<script>
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";

    export let props = {};
</script>

<FloatingTextArea
    label="Please provide a reason for the change"
    bind:value={props.reason}
    rows="3"
    class="w-full"/>
