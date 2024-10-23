<script>
    import { slide } from "svelte/transition";
    import RTE from "@shared/RTE/RTE.svelte";
    import Role from "@shared/Role.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';

    export let continuous_improvement;
    export let staffer = [];
</script>
<div class="mt-4 mb-2">
    <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Suggestion</h3>
</div>
<div class="flex space-x-4 w-full">
    <div class="flex-1">
        <FloatingDate
            bind:value={continuous_improvement.date_of_suggestion}
            label="Date of suggestion"
        />
    </div>
    <div class="flex-1">
        <FloatingCombo 
            bind:value={continuous_improvement.involved_staffs_id}
            items={staffer}
            label="Staffer"
            placeholderText="Select or type staff name"
        />
    </div>
</div>

<span class="ml-2 text-xs text-gray-900/50">Suggestion details</span>
<RTE 
    bind:content={continuous_improvement.suggestion_details}
/>

<div transition:slide={{ duration: 150 }}>
    <div class="mt-4 mb-2">
        <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Action</h3>
    </div>

    <RTE 
        bind:content={continuous_improvement.action_taken}
    />

    <div class="flex space-x-4 w-full mt-2">
        <div class="flex-1">
            <FloatingDate
                bind:value={continuous_improvement.review_date}
                label="Review date"
            />
        </div>

        <div class="flex-1">
            <FloatingCombo 
                bind:value={continuous_improvement.implementing_staffs_id}
                items={staffer}
                label="Reviewer"
                placeholderText="Select or type staff name"
            />
        </div>
    </div>

    <div class="mt-4 mb-2">
        <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Review</h3>
    </div>

    <div class="mt-2">
        <RTE 
            bind:content={continuous_improvement.impact_analysis}
        />
    </div>
</div>





