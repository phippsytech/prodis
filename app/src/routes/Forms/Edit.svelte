<script>
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import FormBuilder from "./FormBuilder.svelte";

    // props
    export let params;

    let formId = params.form_id;

    // Page metadata
    document.title = "Edit Form";
    BreadcrumbStore.set({
        path: [{ url: "/forms", name: "Forms" }, { name: document.title }]
    });

    let formData = {        
        id: 1,
        title: "Form 1",
        creator_name: "John Doe",
        created_at: "2022-01-01T12:00:00Z",
        description: "Lorem Ipsum",
        archived: 0,
        schemas: [
            {
                type: "html",
                label: "Html Title",
                html: "some html here"
            },
            {
                type: "text",      
                key: "email",
                label: "Email Address",
                placeholder: "someone@emailaddress.com"
            },
            {
                type: "textarea",
                label: "Description",
                placeholder: "Describe the thing"
            },
            {
                label: "select the right size",
                key: "size",
                type: "radio",
                options: [
                    {
                        "label": "Small",
                        "option": "small"
                    },
                    {
                        "label": "Medium",
                        "option": "medium"
                    },
                    {
                        "label": "Large",
                        "option": "large"
                    }
                ]
            }
        ]
    };

</script>

<Container>
    <div class="text-2xl mb-2 tracking-tight font-fredoka-one-regular text-indigo-900">
        {document.title}
    </div>
    <FormBuilder bind:formData={formData} />
</Container>