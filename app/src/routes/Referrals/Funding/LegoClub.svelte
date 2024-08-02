<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import ServiceItem from "../ServiceItem.svelte";
    import HourlyItem from "../HourlyItem.svelte";

    let service = {
        selected: false,
        idl: { selected: false, amount: null },
        core: { selected: false, amount: null },
    };
</script>

<div class="grid grid-cols-1 md:grid-cols-[auto_1fr_1fr]">
    <div class="col-span-1 md:col-span-auto">
        <!-- Content of the first column -->
    </div>
    <div class="col-span-1 md:col-span-1">
        <!-- Content of the second column -->
    </div>
    <div class="col-span-1 md:col-span-1">
        <!-- Content of the third column -->
    </div>
</div>

<div class="w-full">
    <label class={service.selected ? "font-bold" : ""}>
        <input type="checkbox" bind:checked={service.selected} />
        Lego Club
    </label>
    {#if service.selected}
        <div class="ml-2">
            <div class="ml-2 text-sm text-slate-400 mb-1">Funding Details</div>
            <div class="flex flex-wrap">
                <div
                    class="bg-indigo-50 w-full flex p-2 pb-0 gap-x-2 items-center grid grid-cols-1 sm:grid-cols-[1fr_1fr_2fr_1fr]"
                >
                    <HourlyItem
                        service={service.idl}
                        label="Initial Enrolment"
                        hours="10"
                        amount="1939.90"
                    />
                </div>
                <div
                    class="w-full flex p-2 pb-0 gap-x-2 items-center grid grid-cols-1 sm:grid-cols-[1fr_1fr_2fr_1fr]"
                >
                    <HourlyItem
                        service={service.idl}
                        label="Re-Enrolment"
                        hours="10"
                        amount="1939.90"
                    />
                </div>
            </div>
        </div>
    {/if}
</div>
