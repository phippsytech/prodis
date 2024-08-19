<script>
    import Spinner from "@shared/PhippsyTech/svelte-ui/Spinner.svelte";
    import { CloudArrowUpIcon } from "heroicons-svelte/24/outline";
    import FileUploader from "@shared/FileUploader.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { formatDate } from "@shared/utilities.js";

    let support_items = [];
    let import_data = {};
    let uploading = false;
    let base64_file = null;
    let error_message = null;

    BreadcrumbStore.set({
        path: [
            { url: "/settings", name: "Settings" },
            { url: null, name: "NDIS Support Items" },
        ],
    });

    jspa("/SupportItem", "listSupportItems", {}).then((result) => {
        support_items = result.result;
    });

    function upload() {
        uploading = true;

        jspa("/SupportItem", "importSupportItem", import_data)
            .then((result) => {
                uploading = false;
                import_data = {};
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
    {#if error_message && import_data.base64_file}
        {error_message}
    {/if}

    <FileUploader
        bind:value={import_data.base64_file}
        bind:name={import_data.file_name}
    />

    {#if import_data.base64_file}
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

    <ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
        {#each support_items as item, index (item.id)}
            <li
                in:slide={{ duration: 200 }}
                out:slide={{ duration: 200 }}
                on:click={() => push("/support_items/" + item.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {support_items.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full"
            >
                <div class="text-xs">
                    {item.support_category_name} &raquo; {item.registration_group_name}
                </div>
                <div class="font-bold">
                    {item.support_item_name}
                    {formatDate(item.start_date)}
                </div>
                <div class="text-xs font-lighter">
                    <nobr><b>Code:</b> {item.support_item_number} &nbsp; </nobr>
                    <nobr><b>Rate:</b> ${item.sa}/hr &nbsp; </nobr>
                </div>
            </li>
        {:else}
            <li
                class="px-4 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default"
            >
                No support items found.
            </li>
        {/each}
    </ul>
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
