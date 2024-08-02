<script>
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    export let props = {};
</script>

<FloatingDate
    bind:value={props.service_agreement_signed_date}
    label="Service Agreement Signed Date"
/>

<FloatingDate
    bind:value={props.service_agreement_end_date}
    label="Service Agreement End Date"
/>

<div class="flex items-center mb-2">
    <input
        type="checkbox"
        id="includeTravel"
        bind:checked={props.is_active}
        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
    />
    <label for="includeTravel" class="ml-2 text-sm font-medium text-gray-900"
        >Active</label
    >
</div>
