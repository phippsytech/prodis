<script>
    import { push } from "svelte-spa-router";
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import { jspa } from "@shared/jspa.js";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

    export let drive_id = null;

    let drives = [];
    let driveList = [];

    onMount(() => {
        jspa("/Google", "listDrives", {})
            .then((result) => {
                driveList = [];
                drives = result.result;

                let selected = false;

                drives.forEach((drive) => {
                    let options = {
                        option: drive.name,
                        value: drive.id,
                        selected: false,
                    };

                    if (drive.id == drive_id) {
                        options.selected = true;
                        selected = true;
                    }

                    driveList.push(options);
                });

                driveList.sort(function (a, b) {
                    const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });

                driveList = driveList;
            })
            .catch((error) => {});
    });
</script>

<FloatingSelect
    bind:value={drive_id}
    label="Select Shared Drive"
    instruction="Select Shared Drive"
    options={driveList}
/>
