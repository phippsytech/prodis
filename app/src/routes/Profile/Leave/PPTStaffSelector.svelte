<script>
    import { onMount } from "svelte";
    import NewFloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    export let staff_id;

    export let readOnly = false;

    // export let value

    let staff = [];
    let staffList = [];
    let selected = false;

    onMount(() => {
        loadStaff(staff_id);
    });

    function loadStaff(staff_id) {
        // if (staff_id == "Choose staffer") return;
        jspa("/Staff", "listStaff", {})
            .then((result) => {
                staff = result.result;

                staff.forEach((staffer) => {
                    let options = {
                        option: staffer.staff_name,
                        value: staffer.id,
                        selected: false,
                    };

                    if (
                        staffer.archived != 1 &&
                        (staffer.employment_basis == "FULLTIME" ||
                            staffer.employment_basis == "PARTTIME" ||
                            staffer.employment_basis == "SALARY")
                    )
                        staffList.push(options);
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
       
     }

     $: {
        if (!selected) staff_id = "Choose staffer"; // unset the selected client_id
     }
  
</script>

<!-- {#if display}

    <div class="bg-white  rounded-sm px-4 py-2 mb-2">
        <div class="text-xs opacity-50">Staff</div> {staff_name}
    </div>
{:else} -->

<!-- <FloatingSelect
    on:change
    bind:value={staff_id}
    label="Staff"
    instruction="Choose staffer"
    options={staffList}
    {readOnly}
/> -->

<NewFloatingSelect
    on:change
    bind:value={staff_id}
    label="Staff"
    instruction="Choose staffer"
    options={staffList}
    {readOnly}
/>
<!-- {/if} -->