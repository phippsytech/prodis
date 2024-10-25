<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import RTE from "@shared/RTE/RTE.svelte";
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

<FloatingInput 
    bind:value={risk.reported_by}
    label="Who reported this risk"
    placeholderText="Select or type staff name"
/>
<!-- <FloatingSelect
    bind:this={riskStatusSelectElement}
    bind:value={risk.status}
    label="Status"
    instruction="Set Status"
    options={riskStatusOptions}
    hideValidation={true}
/> -->

<span class="ml-2 text-xs text-gray-900/50">Description</span>
<RTE 
    bind:content={risk.description}
/>

<div class="mt-4 mb-2">
    <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Resolution</h3>
</div>

<RTE 
    bind:content={risk.resolution}
/>

