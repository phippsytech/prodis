<script context="module">
    import GoogleDrive from "./GoogleDrive.svelte";
    import GoogleDrivePanel from "@app/routes/Settings/Documents/GoogleDrive.svelte.old";
    import { jspa } from "@shared/jspa.js";

    let googleDriveConnected = false;

    export function checkGoogleDriveRoadblock() {
        return new Promise(function (resolve, reject) {
            jspa("/Google", "checkConnected", {}).then((result) => {
                googleDriveConnected = result.result;
                if (googleDriveConnected === true) {
                    resolve(true);
                } else {
                    reject(GoogleDrive);
                }
            });
        });
    }
</script>

<div class="flex items-center justify-center p-4" style="height:90vh">
    <div class="w-full" style="max-width:400px;">
        <div class="text-3xl mb-2">You need to connect to Google Drive</div>
        <p class="mb-4">
            Before you can upload files, you need to connect your Google Drive
            account.
        </p>

        <GoogleDrivePanel />
    </div>
</div>
