<script>
    import { onMount } from "svelte";
    import { createEventDispatcher } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";

    const dispatch = createEventDispatcher();

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

    // function addFile(file){

    //     // check if file is already in the list
    //     if(file_list.find((file_list_item)=>file_list_item.name == file.name)){
    //         return;
    //     }

    //     // only accept CSV files
    //     if(file.type !== 'text/csv') {

    //         toastError(file.name+" is not a CSV file.")
    //         return;
    //     }

    //     if(file.size > 250000){

    //         toastError(file.name+" is too large ("+niceBytes(file.size)+"). Maximum file size is 250 KB.")
    //         return;
    //     }

    //     const fileReader = new FileReader();
    //     fileReader.onload = function(fileLoadedEvent) {

    //         const base64 = fileLoadedEvent.target.result;
    //         file_list.push({
    //             name: file.name,
    //             size: file.size,
    //             type: file.type,
    //             data: base64
    //         });
    //         file_list=file_list;
    //     };
    //     fileReader.readAsDataURL(file);

    //     return;
    // }

    function addFile(file) {
        return new Promise((resolve, reject) => {
            // check if file is already in the list
            if (
                file_list.find(
                    (file_list_item) => file_list_item.name == file.name,
                )
            ) {
                reject(new Error("File already exists in the list"));
                return;
            }

            // only accept CSV files
            if (file.type !== "text/csv") {
                reject(new Error(`${file.name} is not a CSV file`));
                return;
            }

            if (file.size > 250000) {
                reject(
                    new Error(
                        `${file.name} is too large (${niceBytes(file.size)}). Maximum file size is 250 KB.`,
                    ),
                );
                return;
            }

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

    function niceBytes(a) {
        let b = 0,
            c = parseInt(a, 10) || 0;
        for (; 1024 <= c && ++b; ) c /= 1024;
        return (
            c.toFixed(10 > c && 0 < b ? 1 : 0) +
            " " +
            ["bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"][b]
        );
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
            } catch (error) {
                // console.error(`Error adding file: ${file.name}`);
            }
        });

        await Promise.all(promises);

        upload(file_list);
    }

    function upload(file_list) {
        SpinnerStore.set({ show: true, message: "Uploading" });

        jspa(
            "/Invoice/NDIA/PaymentRequestStatus",
            "uploadPaymentRequestStatus",
            file_list,
        )
            .then((result) => {
                if (result.errors) {
                    result.errors.forEach((error) => {
                        toastError(error);
                    });
                }
                toastSuccess("Payment Request CSV Uploaded");
                file_list = [];

                dispatch("uploaded", { message: "Files Uploaded" });
                // push('/remittance/summary');
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
        <div class="font-medium">Drop Payment Request CSV files here</div>
        <div class="opacity-50 py-2">- or -</div>
        <button
            on:click={() => browse()}
            type="button"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
            >Select payment request file(s)</button
        >
    </div>
</div>

<style>
    #drop_zone.active {
        border-color: #4f46e5;
        background: #e0e7ff;
    }
</style>
