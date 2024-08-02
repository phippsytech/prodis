<script>
    import ServiceItem from "../ServiceItem.svelte";

    let service = {
        selected: false,
        idl: { selected: false, amount: null },
        core: { selected: false, amount: null },
    };
</script>

<div class="w-full">
    <label class={service.selected ? "font-bold" : ""}>
        <input type="checkbox" bind:checked={service.selected} />
        Physiotherapy
    </label>
    {#if service.selected}
        <div class="flex flex-wrap">
            <div
                class="bg-indigo-50 w-full flex p-2 pb-0 gap-x-2 items-center grid grid-cols-1 sm:grid-cols-[1fr_2fr_1fr]"
            >
                <ServiceItem
                    selectable={false}
                    service={service.idl}
                    label="Improved Daily Living"
                />
            </div>
        </div>
    {/if}
</div>
