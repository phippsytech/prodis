<script>
    import GoogleDocuments from "@app/routes/GoogleDocuments.svelte";
    import FileUpload from "./FileUpload.svelte";
    import { getClient } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";

    export let params;

    let client_id = params.client_id;
    let folder_id;
    let parent_folder_id;
    let shared_drive;
    let client = {};

    onMount(async () => {
        client = await getClient(client_id);

        await jspa("/Google/SharedDrive", "getSettings", {}).then((result) => {
            shared_drive = result.result.shared_drive;
            // participants_folder = result.result.participants_folder;
            // settings.staff_folder = result.result.staff_folder;
        });

        folder_id = client.google_folder;
        parent_folder_id = client.google_folder;
        if (params && typeof params.folder_id !== "undefined") {
            folder_id = params.folder_id;
        }

        BreadcrumbStore.set({
            path: [
                { name: "Clients", url: "/clients" },
                { name: client.name, url: "/clients/" + client.id },
            ],
        });
    });
</script>

<FileUpload bind:client_id bind:folder_id />

{#if folder_id && parent_folder_id}
    <GoogleDocuments
        base_url="/clients/{client_id}/documents"
        bind:drive_id={shared_drive}
        bind:folder_id
        bind:parent_folder_id
    />
{/if}
