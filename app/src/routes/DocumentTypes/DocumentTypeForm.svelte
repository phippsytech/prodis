<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import Container from "@shared/Container.svelte";

    export let documentType = {
        name: "",
        description: "",
        is_required: false,
    };
</script>

<FloatingInput
    label="Name"
    bind:value={documentType.name}
    placeholder="eg: First Aid"
    autofocus
/>
<FloatingTextArea
    label="Description"
    bind:value={documentType.description}
    placeholder="Provide a short description of the document type so users understand what needs to be provided.  (Optional)"
/>

<Container>
    <RadioButtonGroup
        options={[
            { value: true, option: "Required" },
            { value: false, option: "Optional" },
        ]}
        bind:value={documentType.is_required}
    />
</Container>
