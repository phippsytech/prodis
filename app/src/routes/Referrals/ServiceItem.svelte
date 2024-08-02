<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";

    export let service;
    export let label;
    export let type;
    export let selectable = true;
</script>

{#if selectable}
    <div class="flex-grow-0">
        <input type="checkbox" bind:checked={service.selected} />
        {label}
    </div>
{/if}

<div class="flex-grow ml-4 md:ml-0">
    {#if service.selected || selectable == false}
        <FloatingInput
            label="Amount"
            bind:value={service.amount}
            placeholder="XXX.XX"
        />
    {/if}
</div>
<div class="flex-grow ml-4 md:ml-0">
    {#if service.selected || selectable == false}
        <FloatingSelect
            bind:value={service.funding_source}
            label="Funding Source"
            instruction="Select "
            options={[
                {
                    option: "Improved Daily Living",
                    value: "idl",
                },
                {
                    option: "Improved Relationships",
                    value: "ir",
                },
                {
                    option: "Core Support",
                    value: "core",
                },
            ]}
        />
    {/if}
</div>

<div class="flex-grow ml-4 md:ml-0">
    {#if service.selected || selectable == false}
        <FloatingSelect
            bind:value={service.managed_by}
            label="Payer"
            instruction="Select "
            options={[
                {
                    option: "NDIA/Agency",
                    value: "agency",
                },
                {
                    option: "Plan Manager",
                    value: "plan_manager",
                },
                {
                    option: "Self Managed",
                    value: "self_managed",
                },
            ]}
        />
    {/if}
</div>
