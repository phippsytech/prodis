<script>
    // import { createEventDispatcher } from 'svelte';
    import Select from "svelte-select";
    import { jspa } from "@shared/jspa.js";

    export let selectedValue;

    // let dispatch = createEventDispatcher();

    const getOptionLabel = (option) => option.name;
    //   const getSelectionLabel = (option) => option.name;

    function loadOptions(filterText) {
        filterText = filterText ? filterText.replace(" ", "_") : "";

        let staffList = [];
        return new Promise((resolve, reject) => {
            // to prevent listing everything when the user hasn't typed anything
            // if (filterText.length<3) reject();

            jspa("/Staff", "searchStaff", { search: filterText })
                .then((result) => {
                    let staff = result.result;
                    staff.forEach((staff) => {
                        if (staff.archived != 1)
                            staffList.push({
                                label: staff.name,
                                value: staff.id,
                            });
                    });

                    staffList.sort(function (a, b) {
                        const nameA = a.label.toUpperCase(); // ignore upper and lowercase
                        const nameB = b.label.toUpperCase(); // ignore upper and lowercase
                        if (nameA < nameB) return -1;
                        if (nameA > nameB) return 1;
                        return 0; // names must be equal
                    });

                    staffList = staffList;
                    resolve(staffList);
                })
                .catch((error) => {
                    reject();
                });
        });
    }
</script>

<Select
    bind:value={selectedValue}
    on:change
    {loadOptions}
    {getOptionLabel}
    placeholder="Search staff ..."
></Select>
