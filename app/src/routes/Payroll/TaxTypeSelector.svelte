<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
 
    export let value;

    let options=[
        {
            option: "Manual Adjustment - PAYG",
            value: "PAYGMANUAL"
        },
        {
            option: "Manual Adjustment - PAYG on Schedule 4",
            value: "SCHEDULE4MANUAL"
        },
        {
            option: "Manual Adjustment - PAYG on Schedule 5",
            value: "SCHEDULE5MANUAL"
        },
        {
            option: "Manual Adjustment - STSL Component on Schedule 5",
            value: "SCHEDULE5STSLMANUAL"
        },
        // {
        //     option: "ETP O Manual",
        //     value: "ETPOMANUAL"
        // },
        // {
        //     option: "ETP R Manual",
        //     value: "ETPRMANUAL"
        // },
    ]

</script>


<FloatingSelect bind:value={value} label="Tax Types" options={options} />