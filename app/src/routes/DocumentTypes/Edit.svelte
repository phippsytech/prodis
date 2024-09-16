<script>
    import { onMount } from "svelte";
    import { RolesStore, BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import DocumentTypeForm from "./DocumentTypeForm.svelte";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { push } from "svelte-spa-router";

    import { haveCommonElements } from "@shared/utilities.js";

    export let params;
    let documentTypeId = params.document_type_id;

    let documentType = {
        name: "",
        description: "",
        is_required: false,
    };
    let storedDocumentType = {
        name: "",
        description: "",
        is_required: false,
    };

    let staff = [];

    let mounted = false;

    onMount(async () => {
        jspa("/DocumentType", "getDocumentType", { id: documentTypeId })
            .then((result) => {
                documentType = result.result;

                if (documentType.collect_expiry == "1")
                    documentType.collect_expiry = true;
                if (documentType.collect_expiry == "0")
                    documentType.collect_expiry = false;

                if (documentType.collect_completion == "1")
                    documentType.collect_completion = true;
                if (documentType.collect_completion == "0")
                    documentType.collect_completion = false;
            })
            .finally(() => {
                storedDocumentType = Object.assign({}, documentType);
            });

        jspa("/Staff/document", "listStaffBydocumentId", {
            document_id: documentTypeId,
        }).then((result) => {
            staff = result.result;
        });

        BreadcrumbStore.set({
            path: [{ url: "/documenttypes", name: "Document Types" }],
        });

        mounted = true;
    });

    function undo() {
        documentType = Object.assign({}, storedDocumentType);
    }

    function save() {
        jspa("/DocumentType", "updateDocumentType", documentType).then(
            (result) => {
                // Make a copy of the object
                storedDocumentType = Object.assign({}, documentType);
                push("/documenttypes");
                toastSuccess("Document Type updated successfully");
            },
        );
    }

    function deleteDocumentType() {
        jspa("/DocumentType", "deleteDocumentType", { id: documentType.id })
            .then((result) => {
                push("/documenttypes");
                toastSuccess("Document Type deleted");
            })
            .catch((error) => {
                toastError("Error deleting Document Type");
            });
    }

    $: {
        let show = true;
        let can_delete =
            JSON.stringify(documentType) ===
                JSON.stringify(storedDocumentType) && staff.length == 0;

        if (haveCommonElements($RolesStore, ["super"])) {
            can_delete = true;
        }

        if (mounted) {
            ActionBarStore.set({
                can_delete: can_delete,
                show: show,
                delete: () => deleteDocumentType(),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

<div
    class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular mb-2"
>
    {documentType.name}
</div>
<DocumentTypeForm bind:documentType />
