<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";

    export let service;
    export let label;
    export let hours;
    export let amount;
    export let type;
</script>

<div class="flex-grow-0">
    <input type="checkbox" bind:checked={service.selected} />
    {label}
</div>

<div class="ml-4 md:ml-0 flex">
    <div class="flex-grow">
        {#if service.selected}
            <FloatingInput
                label="Hours"
                bind:value={hours}
                placeholder="XX"
                readOnly={true}
            />
        {/if}
    </div>
    <div class="flex-grow">
        {#if service.selected}
            <FloatingInput
                label="Amount"
                bind:value={amount}
                placeholder="XXX.XX"
                readOnly={true}
            />
        {/if}
    </div>
</div>
<div class="flex-grow ml-4 md:ml-0">
    {#if service.selected}
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
    {#if service.selected}
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
