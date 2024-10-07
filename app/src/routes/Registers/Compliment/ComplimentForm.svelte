<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import Role from "@shared/Role.svelte";

    export let compliment;
    export let staffer = [];
    export let showActionFields = false;

</script>

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
    <div class="mt-2">
        <label class="inline-flex items-center">
            <input type="checkbox" bind:checked={showActionFields} class="form-checkbox" />
            <span class="ml-2 text-xs">Action was taken for this compliment</span>
        </label>
    </div>

    {#if showActionFields}
        <div class="mt-2">
            <FloatingTextArea 
                bind:value={compliment.action_taken}
                label="Action Taken"
                placeholder="Indicate action taken by staff"
            /> 

            <FloatingCombo 
                bind:value={compliment.staffs_id}
                items={staffer}
                label="Acknowledging Staff"
                placeholderText="Select or type staff name"
            />
        </div>
    {/if}
</Role>
