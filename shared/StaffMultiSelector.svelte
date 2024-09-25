<script>
    import FloatingMultiCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingMultiCombo.svelte";
    import { jspa } from "@shared/jspa.js";

    export let staff_id = [];  // Must be an array to store multiple IDs

    let staff = [];
    let staffList = [];

    // Fetch staff list and populate the options
    jspa("/Staff", "listStaff", {})
        .then((result) => {
            staff = result.result;
            staffList = staff
                .filter(staffer => staffer.archived != 1)
                .map(staffer => ({
                    label: staffer.staff_name,  // 'label' for display
                    value: staffer.id            // 'value' for selection (ID)
                }))
                .sort((a, b) => a.label.localeCompare(b.label));  // Sort alphabetically by name
        })
        .catch((err) => {
            console.error("Error fetching staff:", err);
        });

    $: console.log("Selected staff IDs:", staff_id);  // Track selected IDs for debugging
</script>

<FloatingMultiCombo
    label="Staffs"
    items={staffList}  
    bind:values={staff_id} 
    placeholderText="Select or type staff name ..."
    clearable
/>