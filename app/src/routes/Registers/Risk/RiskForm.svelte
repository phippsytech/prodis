<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import { jspa } from "@shared/jspa.js";

    export let risk;
    let staff = []

    jspa("/Staff", "listStaff", {})
        .then((result) => {
            staff = result.result
            .filter((item) => item.archived !== "1")
            .map((item) => ({
                label: `${item.staff_name}`, 
                value: item.id,
            }))
            .sort((a, b) => a.label.localeCompare(b.label));
        })
        .catch(() => {});
</script>

<div class="mt-4 mb-2">
    <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Report</h3>
</div>

<div class="flex space-x-4 w-full">
    <div class="flex-1">
        <FloatingCombo 
            bind:value={risk.staff_id}
            items={staff}
            label="Who reported this risk"
            placeholderText="Select or type staff name"
        />
    </div>
    <div class="flex-1">
        <FloatingInput
            bind:value={risk.type}
            label="Type of Risk"
            placeholder="Type of risk"
        />
    </div>
</div>

<!-- <FloatingSelect
    bind:this={riskStatusSelectElement}
    bind:value={risk.status}
    label="Status"
    instruction="Set Status"
    options={riskStatusOptions}
    hideValidation={true}
/> -->

<FloatingTextArea
    bind:value={risk.description}
    label="Description"
    placeholder="Describe the risk"
    style="height:150px"
/>

<div class="mt-4 mb-2">
    <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Resolution</h3>
</div>

<FloatingTextArea
    bind:value={risk.resolution}
    label="Resolution"
    placeholder="List actions taken to mitigate the risk."
    style="height:150px"
/>
