<script>
    import { jspa } from "@shared/jspa.js";
    import FormActions from "./FormActions.svelte";
    import Edit from "@app/routes/Credentials/Edit.svelte";
    import Role from "@shared/Role.svelte";

    export let params = {};

    let form_id = params.form_id;
    let form = {};
    let component = null;
    let loaded = false;
    let message = "";

    let readOnly = true;

    $: {
        getForm(form_id);
    }

    async function loadComponent(component_name) {
        component = (await import("./Form/" + component_name + ".svelte"))
            .default;
    }

    function getForm(form_id) {
        loaded = false;
        message = "";
        jspa("/SIL/House/Form", "getForm", { id: form_id })
            .then((result) => {
                form = result.result;
                loadComponent(form.report_type);
            })
            .catch((error) => {
                message = "Form not found";
            })
            .finally(() => {
                loaded = true;
            });
    }

    function editForm() {
        readOnly = false;
        // component = Edit;
    }

    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return (
            date.toLocaleDateString([], {
                day: "numeric",
                month: "short",
                year: "numeric",
            }) +
            " - " +
            date.toLocaleTimeString([], {
                hour: "numeric",
                minute: "numeric",
                hour12: true,
            })
        );
    }

    function print() {
        window.print();
    }
</script>

{#if loaded}
    {#if component}
        <div class="flex justify-end items-center mx-4 gap-x-1">
            <Role roles={["sil.admin"]}>
                <button
                    on:click={() => editForm()}
                    type="button"
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >Edit</button
                >
            </Role>

            <button
                on:click={() => print()}
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                <svg
                    class="h-5 w-5"
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
                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"
                    ></path>
                </svg>
            </button>
        </div>

        <svelte:component this={component} bind:form {readOnly} />

        <Role roles={["sil.admin"]}>
            {#if form.edit_history}
                <div class="p-4">
                    <div class="text-xs uppercase font-bold opacity-50">
                        Edit History
                    </div>
                    {#each form.edit_history as edit}
                        <div class="text-sm">
                            <span class="text-xs"
                                >{formatTimestamp(edit.timestamp)} ::
                            </span><span class="font-medium">{edit.reason}</span
                            >
                            <!--(Staff #{edit.staff_id})-->
                        </div>
                    {/each}
                </div>
            {/if}
        </Role>

        {#if !readOnly}
            <FormActions bind:form />
        {/if}
    {:else}
        {message}
    {/if}
{:else}
    <div
        class="flex justify-center items-center h-full text-center text-gray-400"
    >
        <svg
            class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>
        loaded
    </div>
{/if}
