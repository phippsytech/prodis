<script>
    import { XMarkIcon } from "@app/../node_modules/heroicons-svelte/dist/24/outline";
    import { push } from "@app/../node_modules/svelte-spa-router";
    import { onMount } from "svelte";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";

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
        // check if file is already in the list
        if (
            file_list.find((file_list_item) => file_list_item.name == file.name)
        ) {
            return;
        }

        // only accept CSV files
        if (file.type !== "text/csv") {
            file_list.push({
                name: file.name,
                size: file.size,
                type: file.type,
                error: file.name + " is not a CSV file.",
            });
            file_list = file_list;
            return;
        }

        if (file.size > 250000) {
            file_list.push({
                name: file.name,
                size: file.size,
                type: file.type,
                error:
                    file.name +
                    " is too large (" +
                    niceBytes(file.size) +
                    "). Maximum file size is 250 KB.",
            });

            file_list = file_list;
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
            file_list = file_list;
        };
        fileReader.readAsDataURL(file);
        return;
    }

    function fileUpload(evt) {
        let files = evt.dataTransfer.files;

        // Handle the uploaded files here
        for (let i = 0; i < files.length; i++) {
            addFile(files[i]);
        }
    }

    function getFileName(file) {
        return file.name;
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

    function getFileSize(file) {
        return niceBytes(file.size);
    }

    function removeFile(file) {
        file_list = file_list.filter((f) => f.name != file.name);
    }

    // This function lets you select a single file.
    // function browse() {
    //     let input = document.createElement('input');
    //     input.type = 'file';
    //     input.onchange = _ => {
    //         addFile(input.files[0]);

    //     };
    //     input.click();
    // }

    // this function lets you select a multiple file.
    function browse() {
        let input = document.createElement("input");
        input.type = "file";
        input.multiple = true; // Allow multiple file selection
        input.onchange = (_) => {
            for (let i = 0; i < input.files.length; i++) {
                addFile(input.files[i]);
            }
        };
        input.click();
    }

    function clearFiles() {
        file_list = [];
    }

    function upload(file) {
        SpinnerStore.set({ show: true, message: "Uploading" });

        jspa("/Invoice/NDIA/Remittance", "uploadRemittance", file_list)
            .then((result) => {
                toastSuccess("Payment Remittance Uploaded");
                file_list = [];

                push("/remittance/summary");
            })
            .catch((error) => {
                let error_message = error.error_message;

                toastError(error_message);
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }
</script>

<ul role="list" class="divide-y divide-gray-100 bg-white rounded rounded-lg">
    <li class="relative p-2 text-gray-700">
        <div
            id="drop_zone"
            class="flex flex-col border border-dashed border-2 border-gray-300 rounded rounded-lg m-4 justify-center items-center py-10 active:border-indigo-600 bg-gray-50"
            class:active={isDragging}
            bind:this={dropZone}
        >
            <div class="font-medium">Drop remittance files here</div>
            <div class="opacity-50 py-2">- or -</div>
            <button
                on:click={() => browse()}
                type="button"
                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
                >Select remittance file(s)</button
            >
        </div>
    </li>
    {#each file_list as file, index (file.name)}
        <li
            in:slide|global={{ duration: 150, delay: index * 10 }}
            out:slide|global={{ duration: 150, delay: 0 }}
            class="relative flex justify-between items-center py-1 px-5 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 {file.error
                ? 'text-red-800'
                : ''}"
        >
            <div class="flex gap-x-2 items-center">
                <svg
                    class="w-6 h-6 text-white flex-shrink-0"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                    style="filter: drop-shadow(0px 0px 1px rgba(0, 0, 0, 0.5));"
                >
                    <path
                        d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z"
                    ></path>
                    <path
                        d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"
                    ></path>
                </svg>

                {#if file.error}
                    <span>{file.error}</span>
                {:else}
                    <span class="">{getFileName(file)} </span>
                    <span class="text-sm italic">{getFileSize(file)}</span>
                {/if}
            </div>
            <div on:click={() => removeFile(file)}>
                <XMarkIcon class="w-5 h-5 m-2 cursor-pointer" />
            </div>
        </li>
    {/each}

    {#if file_list.length > 0}
        <li class="relative flex justify-between py-4 px-5 text-gray-700">
            <button
                on:click={() => clearFiles()}
                type="button"
                class="block rounded-md border border-indigo-600 px-3 py-2 text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-500 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Remove All</button
            >
            <button
                on:click={() => upload()}
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Upload</button
            >
        </li>
    {/if}
</ul>

<style>
    #drop_zone.active {
        border-color: #4f46e5;
        background: #e0e7ff;
    }
</style>
