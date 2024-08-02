<script>
    import { jspa } from "@shared/jspa.js";
    import { onMount } from "svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { getClient } from "@shared/api.js";
    import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
    import GoogleFolderItem from "../../GoogleFolderItem.svelte";
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";

    export let params;

    let client_id = params.client_id;
    let client = {};
    let shared_drive;
    let participants_folder;
    let mounted = false;

    onMount(async () => {
        client = await getClient(client_id);

        if (client.sil_enabled == "1") client.sil_enabled = true;
        if (client.sil_enabled == "0") client.sil_enabled = false;

        await jspa("/Google/SharedDrive", "getSettings", {}).then((result) => {
            shared_drive = result.result.shared_drive;
            participants_folder = result.result.participants_folder;
        });

        mounted = true;
    });

    function save() {
        jspa("/Client", "updateClient", client).then((result) => {
            toastSuccess("Client updated");
        });
    }
</script>

{#if mounted}
    <Container>
        <h1 class="text-gray-700 text-1xl font-semibold mt-0">SIL Settings</h1>
        <Toggle
            bind:value={client.sil_enabled}
            label_on="Client enabled for SIL"
            label_off="Client not enabled for SIL"
        />
        {#if client.sil_enabled}
            <div class="my-2">
                <FloatingInput
                    bind:value={client.sil_email}
                    label="SIL Email"
                    placeholder="eg: sil@crystelcare.com.au"
                />
            </div>
        {/if}
    </Container>

    <Container>
        <h1 class="text-gray-700 text-1xl font-semibold mt-0">
            Select the Google Drive folder for this participant
        </h1>
        <p class="text-xs mb-4 text-gray-400">
            Click on the folder icon to open/close it. Click on the folder name
            to select it.
        </p>
        <GoogleFolderItem
            bind:selected_folder_id={client.google_folder}
            parent_folder_id={participants_folder}
            drive_id={shared_drive}
        />
        <div class="flex justify-end px-4 mt-2">
            <button
                on:click={() => save()}
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Save</button
            >
        </div>
    </Container>
{/if}
