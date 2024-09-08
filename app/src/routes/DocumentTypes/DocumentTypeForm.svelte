<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";

    export let documentType = {
        name: "",
        description: "",
        is_required: false,
        serviceCollectionOptions: [
            { service_id: 1, collection_option: "required" },
            { service_id: 2, collection_option: "optional" },
            { service_id: 3, collection_option: "do_not_collect" },
            // Add more service collection options as needed
        ],
    };

    let services = [];

    jspa("/Service", "listServices", {}).then((result) => {
        services = result.result.map((service) => {
            const collectionOption =
                documentType.serviceCollectionOptions?.find(
                    (option) => option.service_id === service.id,
                );
            return {
                ...service,
                collection_option: collectionOption
                    ? collectionOption.collection_option
                    : "do_not_collect", // Default to 'optional' if not specified
            };
        });
        console.log(services);
    });
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
    <div class="text-sm font-medium mb-2">Collect Expiry Date?</div>

    <div class="mb-2">
        <RadioButtonGroup
            options={[
                { value: "issued", option: "Issued" },
                { value: "expires", option: "Expiry" },
                { value: "do_not_collect", option: "Do not collect" },
            ]}
            bind:value={documentType.date_collection_option}
        />
    </div>

    {#if documentType.date_collection_option == "issued"}
        <div class="mb-2">
            <FloatingInput
                label="Years until expiry"
                bind:value={documentType.years_until_expiry}
                placeholder="eg: 1"
                autofocus
            />
        </div>
    {/if}
    <!-- <Toggle bind:value={documentType.collect_completion} label_on="Collect Completion Date" label_off="Collect Completion Date" />
<Toggle bind:value={documentType.collect_expiry} label_on="Collect Expiry" label_off="Collect Expiry" /> -->
</Container>

<h3 class="text-slate-800 text-md font-bold mx-2">
    Service Collection Options
</h3>
<div class="text-sm text-slate-800 p-2 pt-0">
    Set which services this document needs to be collected for.
</div>

<div class="bg-white">
    {#each services as service, index}
        <div
            class="flex items-center justify-between pt-2 px-4 {index % 2 === 0
                ? 'bg-transparent'
                : 'bg-slate-50'} hover:bg-indigo-50"
        >
            <div class="flex-shrink mb-2">
                <div class="text-xs font-bold text-slate-800">
                    {service.code}
                </div>
                <div class="text-xs text-gray-500">{service.name}</div>
            </div>
            <div class="flex text-sm font-medium mb-2">
                <RadioButtonGroup
                    options={[
                        { value: "required", option: "Required" },
                        { value: "optional", option: "Optional" },
                        { value: "do_not_collect", option: "Do not collect" },
                    ]}
                    bind:value={service.collection_option}
                />
            </div>
        </div>
    {/each}
</div>
