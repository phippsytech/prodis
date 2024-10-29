<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";

    export let formData = {};

    let selectedType = "input"; // default option

    const options = [
        { option: "HTML Field", value: "html" },  // wysiwyg or raw
        { option: "Input Field", value: "input" },  // text, email, number
        { option: "Text Area", value: "textarea" },
        { option: "Date Field", value: "date" },
        { option: "Selector Field", value: "select" },  // Participant or Staff
        { option: "File Field", value: "file" },
        { option: "Image Field", value: "image" },
        { option: "Checkbox", value: "checkbox" },
        { option: "Radio Button", value: "radio" },
        { option: "Rating Field", value: "rating" },  // Star, Number, etc.
        { option: "Boolean Field", value: "boolean" },
    ];

</script>
<div>
    <FloatingInput bind:value={formData.title} label="Title" placeholder="Form Title"/>
    <FloatingTextArea bind:value={formData.description} label="Description" placeholder="Form Description" style="height:150px"/>
    
    <h3>Schemas</h3>
    <div class="form-builder">
        <div class="flex gap-2">
            <FloatingSelect
                label="Schema Types"
                options={options}
                bind:value={selectedType}
                />
            <button>
                + Add Schema
            </button>
        </div>
        <div class="schema-container flex flex-col gap-2 p-2">
        {#each formData.schemas as schema, index (index)}
            <div class="border border-1 p-4">
                <div class="flex justify-between">
                    <h4>Type: {schema.type}</h4>
                    <div class="actions flex gap-2">
                        <button>Remove</button>
                    </div>
                </div>
                <FloatingInput bind:value={schema.label} label="Label" placeholder={schema.placeholder}/>
                <!-- Add schema components based on selectedType -->
                {#if schema.type === 'input'}
                    <!-- Add Input Field -->
                {:else if schema.type === 'textarea'}
                    <!-- Add Text Area -->
                {:else if schema.type === 'date'}
                    <!-- Add Date Field -->
                {:else if schema.type ==='select'}
                    <!-- Add Selector Field -->
                {:else if schema.type === 'file'}
                    <FloatingInput bind:value={schema.limit} label="Max Limit (mb)" placeholder="25"/>
                {:else if schema.type === 'image'}
                    <!-- Add Image Field -->
                {:else if schema.type === 'checkbox' || schema.type === 'radio'}
                    <h5>Options</h5>
                    {#each schema.options as option, index (index)}
                    <div class="flex gap-2">
                        <FloatingInput bind:value={option.label} label="Option Name" placeholder="Type an option name"/>
                        <FloatingInput bind:value={option.option} label="Option Value" placeholder="Enter option value"/>
                    </div>
                    {/each}
                    <button>Add option</button>
                {:else if schema.type === 'rating'}
                    <!-- Add Rating Field -->
                {:else if schema.type === 'boolean'}
                {/if}
            </div>
        {/each}
        </div>
    </div>
    
    
</div>