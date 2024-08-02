<script>
    import Select from "svelte-select";

    export let selectedValue;

    let internalValue;
    $: selectedValue = internalValue?.value;

    const items = [
        "Travel to participant's home for scheduled support session",
        "Travel to community center for initial assessment meeting with new participant",
        "Travel to participant's school to deliver occupational therapy services",
        "Transporting participant to and from a medical appointment",
        "Assisting participant to attend a community social event",
    ].map((value) => ({ value, label: value }));

    let filterText = "";

    function handleFilter(e) {
        if (e.detail.length === 0 && filterText.length > 0) {
            // items.push({ value: filterText, label: filterText, created: true });
        }
    }

    function handleChange(e) {
        items.forEach((item) => delete item.created);
        selectedValue = e.detail.value;
    }

    const floatingConfig = { strategy: "fixed" };
</script>

<div class="rounded-md shadow-sm ring-1 ring-inset ring-gray-300 bg-white mb-0">
    <h3 class="px-2.5 pt-2 text-xs text-gray-500">Travel Purpose</h3>
    <Select
        containerStyles="border:none; margin:0 0.75rem; padding:0 0; min-height:34px;width:auto;"
        class="select"
        {floatingConfig}
        --list-position="fixed"
        bind:value={internalValue}
        placeholder="Select or type travel purpose ..."
        on:change={handleChange}
        on:filter={handleFilter}
        bind:filterText
        {items}
    >
        <div slot="item" let:item>
            {item.label}
        </div>
    </Select>
    <div class="text-xs bg-gray-200 text-gray-500 px-3 py-1 rounded-b-md">
        Tip: Start typing to enter a different travel purpose
    </div>
</div>

<style>
    :global(.select input::placeholder) {
        color: rgb(17 24 39 / 0.25) !important;
    }
    :global(.item) {
        display: flex !important;
        align-items: center !important;
        cursor: default !important;
        padding: 0.35rem 0.5rem !important;
        height: auto !important;
        line-height: 20px !important;
        line-break: auto;
        white-space: normal !important;
        overflow: visible !important;
        word-wrap: wrap !important;
    }

    :global(.item.active) {
        background: var(--item-is-active-bg, #4f46e5) !important;
        color: var(--item-is-active-color, #fff) !important;
    }

    :global(.item.hover:not(.active)) {
        background: var(--item-hover-bg, #eef2ff) !important;
        color: var(--item-hover-color, inherit) !important;
    }
</style>
