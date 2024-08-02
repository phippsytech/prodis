<script>
    import { jspa } from "@shared/jspa.js";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

    export let drive_id = null;
    export let folder_id = null;

    let folders = [];
    let folderList = [];

    function getFolderList(drive_id) {
        jspa("/Google", "listFolders", { drive_id: drive_id })
            .then((result) => {
                folderList = [];
                folders = result.result;

                let selected = false;

                folders.forEach((folder) => {
                    let options = {
                        option: folder.name,
                        value: folder.id,
                        selected: false,
                    };

                    if (folder.id == folder_id) {
                        options.selected = true;
                        selected = true;
                    }

                    folderList.push(options);
                });

                folderList.sort(function (a, b) {
                    const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });

                folderList = folderList;
            })
            .catch((error) => {});
    }

    $: {
        getFolderList(drive_id);
    }
</script>

<FloatingSelect
    bind:value={folder_id}
    label="Select Staff Folder"
    instruction="Select Folder"
    options={folderList}
/>
