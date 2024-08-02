<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    export let staff_id;

    let staff = [];
    let staffList = [];
    let staffSelectElement = null;

    jspa("/Staff", "listStaff", {})
        .then((result) => {
            staff = result.result;
            staff.forEach((staffer) => {
                if (staffer.archived != 1)
                    staffList.push({
                        option: staffer.staff_name,
                        value: staffer.id,
                    });
            });

            staffList.sort(function (a, b) {
                const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });

            staffList = staffList;
        })
        .catch(() => {});

    function setStaffId(staff_id) {
        staff_id = staff_id;
    }
</script>

<FloatingSelect
    on:change
    bind:this={staffSelectElement}
    bind:value={staff_id}
    bind:option={staff_id}
    label="Staff"
    instruction="Choose staffer"
    options={staffList}
    hideValidation={true}
    {...$$props}
/>
