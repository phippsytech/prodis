<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FileUploader from "@shared/FileUploader.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    // import Camera from '@shared/PhippsyTech/svelte-ui/Camera.svelte';
    import { jspa } from "@shared/jspa.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import Role from "@shared/Role.svelte";

    const date = new Date();

    export let props = {
        id: null,
        credential_id: null,
        staff_id: null,
        details: "",
        vultr_storage_ref: null,

        date_collection_option: "do_not_collect",
        credential_date: null,
    };

    let isReplacing = false;

    // if(props.expires == null && props.collect_expiry){
    //     props.expires = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`
    // }

    function openFile(vultr_storage_ref) {
        SpinnerStore.set({ show: true, message: "Getting File" });

        jspa("/Storage", "getS3ObjectFile", { key: vultr_storage_ref })
            .then((result) => {
                //console.log(result)
                let fileContent = result.result;
                let fileName = vultr_storage_ref.substring(33);

                // Decode base64 content
                let decodedContent = atob(fileContent);
                let byteNumbers = new Array(decodedContent.length);
                for (let i = 0; i < decodedContent.length; i++) {
                    byteNumbers[i] = decodedContent.charCodeAt(i);
                }
                let byteArray = new Uint8Array(byteNumbers);

                // Determine the MIME type based on the file extension or content
                let mimeType;
                if (fileName.endsWith(".pdf")) {
                    mimeType = "application/pdf";
                } else if (fileName.endsWith(".txt")) {
                    mimeType = "text/plain";
                } else if (
                    fileName.endsWith(".jpg") ||
                    fileName.endsWith(".jpeg")
                ) {
                    mimeType = "image/jpeg";
                } else if (fileName.endsWith(".png")) {
                    mimeType = "image/png";
                } else {
                    mimeType = "application/octet-stream";
                }

                const blob = new Blob([byteArray], { type: mimeType });

                const url = URL.createObjectURL(blob);

                // Open the URL in a new tab
                window.open(url, "_blank");

                // Optionally revoke the URL after a short delay
                setTimeout(() => {
                    URL.revokeObjectURL(url);
                }, 10000); // adjust the timeout as needed
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    function replaceFile() {
        isReplacing = true;
    }
</script>

<FloatingInput
    label="Details"
    bind:value={props.details}
    placeholder="eg: document number"
/>

<div class="mb-2">
    {#if props.vultr_storage_ref && !isReplacing}
        <div
            on:click={() => openFile(props.vultr_storage_ref)}
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-pointer"
        >
            <svg
                class="h-4 w-4 inline"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                ></path>
            </svg>
            View Credential
        </div>
        <Role roles={["admin"]}>
        <button
            on:click={replaceFile}
            class="ml-2 text-sm text-gray-600 underline"
        >
            Replace File
        </button>
        </Role>
    {:else}
        <FileUploader
            bind:value={props.base64_file}
            bind:extension={props.file_extension}
        />
    {/if}
</div>
<!-- {#if props.photo}
    <img src={props.photo} class="w-full" />
    <a on:click={()=>props.photo=null} class="text-blue-600">try again</a>
{:else}
    <Camera bind:value={props.photo} />
{/if} -->

{#if props.date_collection_option == "issued"}
    <FloatingDate label="Issued" bind:value={props.credential_date} />
{/if}
{#if props.date_collection_option == "expires"}
    <FloatingDate label="Expires" bind:value={props.credential_date} />
{/if}
