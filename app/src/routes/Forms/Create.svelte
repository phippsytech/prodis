<script>
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import FormBuilder from "./FormBuilder.svelte";

    // Page metadata
    document.title = "Create Form";
    BreadcrumbStore.set({
        path: [{ url: "/forms", name: "Forms" }, { name: document.title }]
    });

    let formData = {
        title: "",
        description: "",
        schemas: []
    };

</script>

<Container>
    <div class="text-2xl mb-2 tracking-tight font-fredoka-one-regular text-indigo-900">
        {document.title}
    </div>
    <FormBuilder bind:formData={formData} />
</Container>