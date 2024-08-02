<script>
    import GoogleDocuments from "@app/routes/GoogleDocuments.svelte";
    import { getStaffer } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";

    export let params;

    let staff_id = params.staff_id;
    let folder_id;
    let parent_folder_id;
    let shared_drive;
    let staffer = {};

    onMount(async () => {
        staffer = await getStaffer(staff_id);

        await jspa("/Google/SharedDrive", "getSettings", {}).then((result) => {
            shared_drive = result.result.shared_drive;
            // participants_folder = result.result.participants_folder;
            // settings.staff_folder = result.result.staff_folder;
        });

        folder_id = staffer.google_folder;
        parent_folder_id = staffer.google_folder;
        if (params && typeof params.folder_id !== "undefined") {
            folder_id = params.folder_id;
        }

        BreadcrumbStore.set({
            path: [
                { name: "Staff", url: "/staffs" },
                { name: staffer.name, url: "/staffs/" + staffer.id },
            ],
        });
    });
</script>

{#if folder_id && parent_folder_id}
    <GoogleDocuments
        base_url="/staffs/{staff_id}/documents"
        bind:drive_id={shared_drive}
        bind:folder_id
        bind:parent_folder_id
    />
{/if}
