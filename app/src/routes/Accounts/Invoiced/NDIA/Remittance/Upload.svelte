<script>
    import { push } from "svelte-spa-router";
    import Spinner from "@shared/PhippsyTech/svelte-ui/Spinner.svelte";
    import { CloudArrowUpIcon } from "heroicons-svelte/24/outline";
    import FileUploader from "@shared/FileUploader.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import { jspa } from "@shared/jspa.js";

    let uploading = false;
    let remittance = {};

    let error_message = null;

    let breadcrumbs_path = [
        { url: "/invoicing", name: "Invoicing" },
        { url: "/invoicing/ndia", name: "NDIA" },
        { url: "/invoicing/ndia/remittance", name: "Remittance" },
    ];

    function upload() {
        uploading = true;

        jspa("/PaymentRemittance", "uploadPaymentRemittance", remittance)
            .then((result) => {
                uploading = false;
                remittance = {};
                // push('/clients/'+params.id)
            })
            .catch((error) => {
                error_message = error.error_message;
            })
            .finally(() => {
                uploading = false;
            });
    }
</script>

{#if !uploading}
    {#if error_message && remittance.base64_file}
        {error_message}
    {/if}

    <FileUploader
        bind:value={remittance.base64_file}
        bind:name={remittance.file_name}
    />

    {#if remittance.base64_file}
        <div class="flex justify-between">
            <span></span>
            <button
                on:click={() => upload()}
                type="button"
                class="inline-block px-6 py-4 bg-blue-600 text-white font-medium text-sm leading-snug uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                ><CloudArrowUpIcon class="w-5 h-5 inline" /> Upload</button
            >
        </div>
    {/if}
{:else}
    <div class="flex items-center justify-center p-4" style="height:100vh">
        <div class="w-full" style="max-width:400px;">
            <div class="text-center">
                <Spinner />
                <p class="text-gray-700 uppercase mb-2">Uploading File</p>
                <p>This can take a moment...</p>
            </div>
        </div>
    </div>
{/if}
