<script>
    import { onMount } from "svelte";
    import { ModalStore } from "@app/Overlays/stores.js";
    import { haveCommonElements } from "@shared/utilities.js";
        import { RolesStore } from "@shared/stores.js";
    import Credential from "./Credential.svelte";
    import { jspa } from "@shared/jspa.js";
    import { formatDate } from "@shared/utilities.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    $: modal = $ModalStore;
    $: rolesStore = $RolesStore;

    export let credential = {};
    export let staff_id;
    export let required = false;

    let staff_credential = {};
    let loaded = false;

    onMount(async () => {
        jspa("/Staff/Credential", "getCredential", {
            staff_id: staff_id,
            credential_id: credential.id,
        })
            .then((result) => {
                staff_credential = result.result;
                // if(result.result.length > 0) staff_credential = result.result;//[0];
            })
            .finally(() => {
                loaded = true;
            });
    });

    function updateCredential() {

        if (haveCommonElements(rolesStore, ["auditor"])) {
            toastError("Auditors cannot update credentials");
            return;
        }


        // make a copy of the props so if the data is cleared we can still use it
        let data = Object.assign({}, modal.props);

        data.credential_id = credential.id;
        data.staff_id = staff_id;

        if (!data.id) {
            SpinnerStore.set({ show: true, message: "Adding Credential" });
            jspa("/Staff/Credential", "addCredential", data)
                .then((result) => {
                    staff_credential = result.result;
                })
                .finally(() => {
                    SpinnerStore.set({ show: false });
                });
        } else {
            SpinnerStore.set({ show: true, message: "Updating Credential" });
            jspa("/Staff/Credential", "updateCredential", data)
                .then((result) => {
                    staff_credential = result.result;
                })
                .finally(() => {
                    SpinnerStore.set({ show: false });
                });
        }
    }

    function deleteCredential() {

        if (haveCommonElements(rolesStore, ["auditor"])) {
            toastError("Auditors cannot delete credentials");
            return;
        }


        SpinnerStore.set({ show: true, message: "Deleting Credential" });
        jspa("/Staff/Credential", "deleteCredential", {
            id: staff_credential.id,
        })
            .then((result) => {
                staff_credential = {};
            })
            .catch((error) => {
                toastError(error.message);
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    function editCredential() {
        (staff_credential.date_collection_option =
            credential.date_collection_option),
            ModalStore.set({
                label: credential.name,
                description: credential.description,
                show: true,
                props: staff_credential,
                component: Credential,
                action_label: "Update",
                action: () => updateCredential(),
                delete: () => deleteCredential(),
            });
    }

    $: valid = () => isValid();

    function isValid() {
        if (staff_credential) {
            if (Object.keys(staff_credential).length == 0) {
                return false;
            }

            if (staff_credential.id == null) {
                return false;
            }

            if (staff_credential.credential_date == null) return false;

            let credential_expires = new Date(staff_credential.credential_date);

            if (credential.date_collection_option == "issued") {
                const expiry_year =
                    credential_expires.getFullYear() +
                    parseInt(credential.years_until_expiry);
                credential_expires.setFullYear(expiry_year);

                if (credential_expires < new Date()) return false;
            }

            if (credential.date_collection_option == "expires") {
                if (!staff_credential.credential_date) return false;
                if (credential_expires < new Date()) return false;
            }

            return true;
        } else {
            return false;
        }
    }
</script>

{#if loaded}
    <div
        on:click={() => editCredential()}
        class="relative flex items-center space-x-3 rounded-lg border {valid()
            ? 'border-green-300 bg-green-50'
            : required || staff_credential.credential_date
              ? 'border-red-300 bg-red-50'
              : 'border-gray-300 bg-white'} px-4 py-3 shadow-sm hover:bg-indigo-600 hover:text-white group"
    >
        <div class="flex-shrink-0">
            {#if valid()}
                <svg
                    class="h-6 w-6 text-green-600"
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
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
            {:else if required || staff_credential.credential_date}
                <svg
                    class="h-6 w-6 text-red-600"
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
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                    ></path>
                </svg>
            {:else}
                <svg
                    class="h-6 w-6 text-gray-500"
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
                        d="M12 6v12m6-6H6"
                    ></path>
                </svg>
            {/if}
        </div>

        <div class="min-w-0 flex-1">
            <span class="focus:outline-none">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p
                    class="text-sm font-medium group-hover:text-white flex justify-between"
                >
                    {credential.name}

                    {#if staff_credential.vultr_storage_ref}
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
                                d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"
                            ></path>
                        </svg>
                    {/if}
                </p>

                {#if credential.description}
                    <p class="text-xs text-gray-600">
                        {credential.description}
                    </p>
                {/if}

                {#if staff_credential && Object.keys(staff_credential).length > 0}
                    {#if staff_credential.details}
                        <div class="text-sm">
                            {staff_credential.details}
                        </div>{/if}

                    {#if credential.date_collection_option == "expires" && staff_credential.credential_date}
                        <div class="text-xs font-light">
                            Expires: {formatDate(
                                staff_credential.credential_date,
                            )}
                        </div>
                    {/if}

                    {#if credential.date_collection_option == "issued" && staff_credential.credential_date}
                        <div class="text-xs font-light">
                            Issued: {formatDate(
                                staff_credential.credential_date,
                            )}
                        </div>
                    {/if}
                {:else}{/if}
            </span>
        </div>
    </div>
{:else}
    <div
        class="relative flex justify-center items-center space-x-3 rounded-lg bg-white px-4 py-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:bg-indigo-600 hover:text-white group"
    >
        <svg
            class="animate-spin flex-shrink-0 h-5 w-5 text-indigo-600"
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
    </div>
{/if}

<!-- CE: {credential_expires} -->
