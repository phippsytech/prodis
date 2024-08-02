<script>
    import Select from "svelte-select";

    export let selectedValue;
    // let internalValue = null;
    let internalValue = selectedValue
        ? { value: selectedValue, label: selectedValue }
        : null;
    let filterText = "";

    let select; // reference to the select component

    $: selectedValue = internalValue ? internalValue.value : filterText;

    const items = [
        "General drive",
        "Regulation drive",
        "School run",
        "Taking participant out for food",
        "Taking participant to a medical appointment",
        "Taking participant to attend an activity / community social event",
        "Taking participant to the shops",
        "Travel for meeting with carers",
        "Travel for meeting with parent/guardians",
        "Travel for meeting with stakeholders",
        "Travel for meeting with teacher",
        "Travel for meeting with participant",
        "Travel for scheduled session with participant",
        "Travel to community centre for scheduled session",
        "Travel to deliver services to participant",
        "Travel to drop off resources to participant",
    ].map((value) => ({ value, label: value }));

    function handleBlur() {
        if (!internalValue && filterText != "") {
            internalValue = { value: filterText, label: filterText };
        }
    }

    export function clear() {
        select.handleClear();
    }

    const floatingConfig = { strategy: "fixed" };
</script>

<div
    class="rounded-md shadow-sm ring-1 ring-inset ring-indigo-100 bg-white mb-0"
>
    <h3 class="px-2.5 pt-2 text-xs text-gray-500">Travel Purpose</h3>

    <Select
        bind:this={select}
        containerStyles="border:none; margin:0 0.75rem; padding:0 0; min-height:34px;width:auto;"
        class="select"
        {floatingConfig}
        --list-position="fixed"
        bind:value={internalValue}
        on:blur={handleBlur}
        bind:filterText
        {items}
        placeholder="Select or type travel purpose ..."
        hideEmptyState
    >
        <div slot="item" let:item>
            {item.label}
        </div>
    </Select>
    <div class="text-xs bg-indigo-50 text-gray-500 px-3 py-1 rounded-b-md">
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
