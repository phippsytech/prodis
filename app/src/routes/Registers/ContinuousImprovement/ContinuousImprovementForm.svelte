<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';

    export let continuous_improvement;
    export let staffer = [];
    export let readOnly = false;

</script>

<div class="flex space-x-4 w-full">
    <div class="flex-1">
        <FloatingDate
            bind:value={continuous_improvement.date_of_suggestion}
            label="Date of suggestion"
            {readOnly}
        />
    </div>
    <div class="flex-1">
        <FloatingCombo 
            bind:value={continuous_improvement.involved_staffs_id}
            items={staffer}
            label="Involved Staff"
            placeholderText="Select or type staff name"
            {readOnly}
        />
    </div>
</div>

<span class="ml-2 text-xs text-gray-900/50">Suggestion details</span>
<RTE 
    bind:content={continuous_improvement.suggestion_details}
/>

<div class="flex space-x-4 w-full mt-2">
    <div class="flex-1">
        <FloatingDate
            bind:value={continuous_improvement.implementation_date}
            label="Implementation date"
            {readOnly}
        />
    </div>

    <div class="flex-1">
        <FloatingCombo 
            bind:value={continuous_improvement.implementing_staffs_id}
            items={staffer}
            label="Implementing Staff"
            placeholderText="Select or type staff name"
            {readOnly}
        />
    </div>
</div>

<FloatingTextArea 
    bind:value={continuous_improvement.action_taken}
    label="Action Taken"
    placeholder="Indicate action taken by staff"
    {readOnly}
/> 

<div class="flex space-x-4 w-full">
    <div class="flex-1">
        <FloatingDate
            bind:value={continuous_improvement.review_date}
            label="Review date"
            {readOnly}
        />
    </div>

    <div class="flex-1">
        <FloatingDate
            bind:value={continuous_improvement.completion_date}
            label="Completion date"
            {readOnly}
        />
    </div>
</div>

<FloatingTextArea 
    bind:value={continuous_improvement.impact_analysis}
    label="Impact Analysis"
    placeholder="Indicate summary of the impact the suggestion"
    {readOnly}
/> 


