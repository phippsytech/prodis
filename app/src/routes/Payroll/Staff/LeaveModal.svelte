<script>
    import FloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    export let props;

    let leave_types=[
        {option:"Annual Leave", value:"annual"},
        {option:"Carer's Leave (unpaid)", value:"carer"},
        {option:"Community Service Leave", value:"community"},
        {option:"Compassionate Leave (paid)", value:"paid_compassionate"},
        {option:"Compassionate Leave (unpaid)", value:"unpaid_compassionate"},
        {option:"Long Service Leave", value:"long_service"},
        {option:"Other Unpaid Leave", value:"other_unpaid"},
        {option:"Parental Leave (unpaid)", value:"parental"},
        {option:"Personal (Sick/Carer's) Leave", value:"personal"}
    ]


</script>



<FloatingSelect bind:value={props.type} label="Leave Type" instruction="Choose Leave Type" options={leave_types} />
<FloatingInput bind:value={props.quantity} label="Quantity" type="number" instruction="Enter Quantity" />
