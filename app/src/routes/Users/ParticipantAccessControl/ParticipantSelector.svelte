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

        let participantList = [];
        return new Promise((resolve, reject) => {
            // to prevent listing everything when the user hasn't typed anything
            // if (filterText.length<3) reject();

            jspa("/Client", "searchClients", { search: filterText })
                .then((result) => {
                    let participant = result.result;
                    participant.forEach((participant) => {
                        if (participant.archived != 1)
                            participantList.push({
                                label: participant.name,
                                value: participant.id,
                            });
                    });

                    participantList.sort(function (a, b) {
                        const nameA = a.label.toUpperCase(); // ignore upper and lowercase
                        const nameB = b.label.toUpperCase(); // ignore upper and lowercase
                        if (nameA < nameB) return -1;
                        if (nameA > nameB) return 1;
                        return 0; // names must be equal
                    });

                    participantList = participantList;
                    resolve(participantList);
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
    placeholder="Search participants ..."
></Select>
