<script>
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';

    let feedbackStatusSelectElement = null;

    let feedbackStatusOptions = [
        {option: "Open", value: "open"},
        {option: "Closed", value: "closed"},
    ];

    export let feedback={
        status: "open",
        type: "compliment",
        name: null,
        email: null,
        phone: null,
        message: null,
        resolution: null,
    };

    export let readOnly =false

    $: {
        readOnly = feedback.status=="closed"
    }

</script>
    
<FloatingSelect
    bind:this={feedbackStatusSelectElement}
    bind:value={feedback.status}
    label="Status"
    instruction="Set Status"
    options={feedbackStatusOptions}  
    hideValidation={true}
/>

<FloatingSelect
    bind:value={feedback.type}
    label="Type of Feedback"
    instruction="Choose feedback type"
    options={[
        {
            option: "Compliment",
            value: "compliment"
        },
        {
            option: "Complaint",
            value: "complaint"
        }
    ]}
    hideValidation={true}
    {readOnly}
/>

<FloatingInput bind:value={feedback.name} label="Name" placeholder="Name of person" {readOnly}/>
<FloatingInput bind:value={feedback.email} label="Email" placeholder="Email address of person" {readOnly}/>
<FloatingInput bind:value={feedback.phone} label="Phone" placeholder="Phone number of person" {readOnly}/>
<FloatingTextArea bind:value={feedback.message} label="Message" placeholder="Message" style="height:150px" {readOnly}/>
<FloatingTextArea bind:value={feedback.resolution} label="Resolution" placeholder="List actions taken to resolve the feedback." style="height:150px" {readOnly}/>