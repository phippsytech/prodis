<script>
    import ServiceItem from "../ServiceItem.svelte";

    let service = {
        selected: true,
        core: { selected: false, amount: null },
        idl_sbis: { selected: false, amount: null },
        idl_bmp: { selected: false, amount: null },
        ir_sbis: { selected: false, amount: null },
        ir_bmp: { selected: false, amount: null },
    };
</script>

<div class="w-full">
    <label class={service.selected ? "font-bold" : ""}>
        <input type="checkbox" bind:checked={service.selected} />
        Positive Behaviour Supports
    </label>
    {#if service.selected}
        <div class="flex flex-wrap">
            <div
                class="bg-indigo-50 w-full flex p-2 pb-0 gap-x-2 items-center grid grid-cols-1 sm:grid-cols-[1fr_1fr_2fr_1fr]"
            >
                <ServiceItem service={service.idl_sbis} label="SBIS" />
            </div>
            <div
                class=" w-full flex p-2 pb-0 gap-x-2 items-center grid grid-cols-1 sm:grid-cols-[1fr_1fr_2fr_1fr]"
            >
                <ServiceItem service={service.idl_bmp} label="BMP" />
            </div>
        </div>
    {/if}
</div>
