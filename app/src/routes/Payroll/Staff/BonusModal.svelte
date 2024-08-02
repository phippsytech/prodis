<script>
    import FloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    
    export let props;

</script>

<FloatingSelect bind:value={props.type} label="Bonus" instruction="Choose bonus" options={[
    {option:"Bonus ($60)", value: "bonus"},
    {option:"Referral Bonus ($100)", value: "referral"}
]} />
<FloatingInput bind:value={props.quantity} label="Quantity" type="number" instruction="Enter Quantity" />
