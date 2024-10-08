<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import RTEView from "@shared/RTE/RTE.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import Role from "@shared/Role.svelte";

    export let continuous_improvements;
    export let staffer = [];
    export let readOnly = false;

</script>

<FloatingDate
    bind:value={continuous_improvements.date_of_suggestion}
    label="Date of compliment"
    {readOnly}
/>

<RTE 
    bind:content={continuous_improvements.suggestion_details}
/>

<FloatingCombo 
    bind:value={continuous_improvements.involved_staffs_id}
    items={staffer}
    label="Involved Staff"
    placeholderText="Select or type staff name"
    {readOnly}
/>

<FloatingTextArea 
    bind:value={continuous_improvements.action_taken}
    label="Action Taken"
    placeholder="Indicate action taken by staff"
    {readOnly}
/> 


<FloatingDate
    bind:value={continuous_improvements.review_date}
    label="Review date"
    {readOnly}
/>

<FloatingDate
    bind:value={continuous_improvements.implementation_date}
    label="Implementation date"
    {readOnly}
/>

<FloatingTextArea 
    bind:value={continuous_improvements.impact_analysis}
    label="Impact Analysis"
    placeholder="Indicate summary of the impact the suggestion"
    {readOnly}
/> 

<FloatingCombo 
    bind:value={continuous_improvements.implementing_staffs_id}
    items={staffer}
    label="Implementing Staff"
    placeholderText="Select or type staff name"
    {readOnly}
/>

<FloatingDate
    bind:value={continuous_improvements.implementation_date}
    label="Completion date"
    {readOnly}
/>
