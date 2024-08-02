<script>

    import Container from '@shared/Container.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';

    export let props

    props.note =""

</script>

<FloatingTextArea bind:value={props.note} label="Notes" placeholder="eg: Extra Notes." style="height:350px"  />
    