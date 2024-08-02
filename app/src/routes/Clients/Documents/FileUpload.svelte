<script>
    import { onMount } from "svelte";
    import { createEventDispatcher } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    const dispatch = createEventDispatcher();

    export let client_id;
    export let folder_id;

    let dropZone;
    let isDragging = false;
    let file_list = [];

    onMount(() => {
        dropZone.ondragover = dropZone.ondragenter = function (evt) {
            isDragging = true;
            evt.preventDefault();
        };

        dropZone.ondragleave = function (evt) {
            isDragging = false;
            evt.preventDefault();
        };

        dropZone.ondrop = function (evt) {
            // Call the function to handle file upload
            fileUpload(evt);
            isDragging = false;
            evt.preventDefault();
        };
    });

    function addFile(file) {
        return new Promise((resolve, reject) => {
            const fileReader = new FileReader();
            fileReader.onload = function (fileLoadedEvent) {
                const base64 = fileLoadedEvent.target.result;
                file_list.push({
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    data: base64,
                });
                resolve();
            };
            fileReader.onerror = function (error) {
                reject(error);
            };
            fileReader.readAsDataURL(file);
        });
    }

    function fileUpload(evt) {
        let files = evt.dataTransfer.files;

        // Handle the uploaded files here
        uploadFiles(files);
    }

    function browse() {
        let input = document.createElement("input");
        input.type = "file";
        input.multiple = true; // Allow multiple file selection
        input.onchange = (_) => {
            uploadFiles(input.files);
        };
        input.click();
    }

    async function uploadFiles(files) {
        const fileArray = Array.from(files);
        const promises = fileArray.map(async (file) => {
            try {
                await addFile(file);
            } catch (error) {}
        });
        await Promise.all(promises);
        upload(file_list);
        file_list = [];
    }

    function upload(file_list) {
        SpinnerStore.set({ show: true, message: "Uploading" });

        jspa("/Client/Document", "uploadClientDocument", {
            client_id: client_id,
            file_list: file_list,
            folder_id: folder_id,
        })
            .then((result) => {
                if (result.errors) {
                    result.errors.forEach((error) => {
                        toastError(error);
                    });
                }
                toastSuccess("File(s) Uploaded");
                file_list = [];

                dispatch("uploaded", { message: "File(s) Uploaded" });
            })
            .catch((error) => {
                let error_message = error.error_message;

                toastError(error_message);
                file_list = [];
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }
</script>

<div class=" bg-white rounded rounded-lg">
    <div
        id="drop_zone"
        class="flex flex-col border border-dashed border-2 border-gray-300 rounded rounded-lg m-4 justify-center items-center py-10 active:border-indigo-600 bg-gray-50"
        class:active={isDragging}
        bind:this={dropZone}
    >
        <div class="font-medium">Drop files here</div>
        <div class="opacity-50 py-2">- or -</div>
        <button
            on:click={() => browse()}
            type="button"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
            >Select file(s)</button
        >
    </div>
</div>

<style>
    #drop_zone.active {
        border-color: #4f46e5;
        background: #e0e7ff;
    }
</style>
