<script>
    // import CheckboxGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxGroup.svelte';
    import { onMount } from "svelte";
    import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";
    import { jspa } from "@shared/jspa.js";
    import { HouseStore } from "@shared/stores.js";

    export let values = [];

    let house_staff = [];
    let staffList = [];

    let staff_fetch = false;

    $: houseStore = $HouseStore;
    $: {
        if (
            houseStore.id != null &&
            staffList.length == 0 &&
            staff_fetch == false
        ) {
            staff_fetch = true;
            jspa("/SIL/House/Staff", "listHouseStaffByHouseId", {
                house_id: houseStore.id,
            }).then((result) => {
                house_staff = result.result;
                house_staff.forEach((staffer) => {
                    if (staffer.archived != 1)
                        staffList.push({
                            option: staffer.name,
                            value: staffer.staff_id,
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
                staffList.forEach((option) => {
                    if (values)
                        option.checked = values.some(
                            (item) => item == option.value,
                        );
                });
            });
        }
    }
</script>

<CheckboxButtonGroup options={staffList} bind:values />
