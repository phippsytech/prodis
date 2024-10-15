<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import RTEView from "@shared/RTE/RTE.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import Role from "@shared/Role.svelte";

    export let compliment;
    export let staffer = [];
</script>
<div class="mt-4 mb-2">
    <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Compliment</h3>
</div>

<div class="flex space-x-4">
    <div class="flex-1">
        <FloatingInput 
            bind:value={compliment.complimenter}
            label="Name of complimenter"
            placeholder="eg: Eva Snow"
        />
    </div>

    <div class="flex-1">
        <FloatingDate
            bind:value={compliment.date}
            label="Date of compliment"
        />
    </div>
</div>

<span class="ml-2 text-xs text-gray-900/50">Compliment</span>

<RTE 
    bind:content={compliment.description}
/>


<Role roles={["admin"]}>
    <div class="mt-4 mb-2">
        <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Action</h3>
    </div>

    <div class="mt-2">
        <FloatingCombo 
            bind:value={compliment.staffs_id}
            items={staffer}
            label="Acknowledging Staff"
            placeholderText="Select or type staff name"
        />
        <FloatingTextArea 
            bind:value={compliment.action_taken}
            label="Action Taken"
            placeholder="Indicate action taken by staff"
        /> 
    </div>
</Role>
