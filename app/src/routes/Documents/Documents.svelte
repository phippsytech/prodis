<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import GoogleDocuments from "../../routes/GoogleDocuments.svelte";

    export let params;
    let folder_id; // = "1-tTFtKFGcaC_5meNuxxTP-FG5AiYpKtE";
    let parent_folder_id;

    jspa("/SIL/House", "getHouse", { id: 1 })
        .then((result) => {
            const house = result.result;
            folder_id = house.google_folder;
            parent_folder_id = house.google_folder;

            if (params && params.folder_id !== "undefined") {
                folder_id = params.folder_id;
            }
        })
        .catch((error) => {});
</script>

{#if folder_id && parent_folder_id}
    <GoogleDocuments bind:folder_id bind:parent_folder_id />
{/if}
