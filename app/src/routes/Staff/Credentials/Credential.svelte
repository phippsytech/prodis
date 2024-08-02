<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FileUploader from "@shared/FileUploader.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    // import Camera from '@shared/PhippsyTech/svelte-ui/Camera.svelte';
    import { jspa } from "@shared/jspa.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";

    const date = new Date();

    export let props = {
        id: null,
        credential_id: null,
        staff_id: null,
        details: "",
        google_file_ref: null,

        date_collection_option: "do_not_collect",
        credential_date: null,
    };

    // if(props.expires == null && props.collect_expiry){
    //     props.expires = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`
    // }

    function openFile(file_id) {
        SpinnerStore.set({ show: true, message: "Getting File" });
        jspa("/Google", "getFile", { file_id: file_id })
            .then((result) => {
                let url = result.result;
                window.open(url, "_blank");
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }
</script>

<FloatingInput
    label="Details"
    bind:value={props.details}
    placeholder="eg: document number"
/>

<div class="mb-2">
    {#if props.google_drive_file_ref}
        <div
            on:click={() => openFile(props.google_drive_file_ref)}
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
