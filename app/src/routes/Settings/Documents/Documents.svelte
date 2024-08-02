<script>
    import Container from "@shared/Container.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import { jspa } from "@shared/jspa.js";

    import ListDrives from "./ListDrives.svelte";
    import ListFolders from "./ListFolders.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    BreadcrumbStore.set({
        path: [
            { url: "/settings", name: "Settings" },
            { url: null, name: "Documents" },
        ],
    });

    let googleConnected = false;

    let settings = {};

    jspa("/Google", "checkConnected", {}).then((result) => {
        googleConnected = result.result;
    });

    function connectGoogle() {
        jspa("/Google", "connect", {}).then((result) => {
            document.location = result.result;
        });
    }

    function revokeGoogle() {
        jspa("/Google", "revoke", {}).then((result) => {
            toastSuccess("Google Drive connection revoked");
            googleConnected = false;
        });
    }

    function createStaffFolders() {
        SpinnerStore.set({ show: true, message: "Creating Staff Folders" });
        jspa("/Staff", "createGoogleDriveFolders", {})
            .then((result) => {
                toastSuccess("Staff folders created");
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    function createClientFolders() {
        SpinnerStore.set({ show: true, message: "Creating Client Folders" });
        jspa("/Client", "createGoogleDriveFolders", {})
            .then((result) => {
                toastSuccess("Client folders created");
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    jspa("/Google/SharedDrive", "getSettings", {}).then((result) => {
        settings.shared_drive = result.result.shared_drive;
        settings.participants_folder = result.result.participants_folder;
        settings.staff_folder = result.result.staff_folder;
    });

    function save() {
        jspa("/Google/SharedDrive", "setSettings", { settings }).then(
            (result) => {},
        );
    }
</script>

<div class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular">
    Google Drive
</div>

{#if !googleConnected}
    <div class="my-4">
        <Button
            on:click={() => connectGoogle()}
            label="Connect to Google Drive"
        />
    </div>
{:else}
    <Container>
        Select Shared Drive
        <ListDrives bind:drive_id={settings.shared_drive} />
    </Container>

    {#if settings.shared_drive && settings.shared_drive != "Select Shared Drive"}
        <Container>
            Select Participants Folder
            <ListFolders
                bind:drive_id={settings.shared_drive}
                bind:folder_id={settings.participants_folder}
            />
        </Container>

        <Container>
            Select Staff Folder
            <ListFolders
                bind:drive_id={settings.shared_drive}
                bind:folder_id={settings.staff_folder}
            />
        </Container>

        {#if settings.participants_folder && settings.participants_folder != "Select Folder" && settings.staff_folder && settings.staff_folder != "Select Folder"}
            <div class="flex justify-end px-4">
                <button
                    on:click={() => save()}
                    type="button"
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >Save</button
                >
            </div>
        {/if}
    {/if}

    <Container>
        <div class="my-4">
            <Button
                on:click={() => createStaffFolders()}
                label="Create Folders for Staff in Google Drive"
            />
            <Button
                on:click={() => createClientFolders()}
                label="Create Folders for Clients in Google Drive"
            />
        </div>
    </Container>
    <div class="my-4">
        <Button
            on:click={() => revokeGoogle()}
            label="Revoke connection to Google Drive"
        />
    </div>
{/if}
