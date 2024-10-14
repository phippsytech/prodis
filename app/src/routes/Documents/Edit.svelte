<script>
  import { onMount } from "svelte";
  import { RolesStore, BreadcrumbStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import DocumentForm from "./DocumentForm.svelte";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { push } from "svelte-spa-router";

  import { haveCommonElements } from "@shared/utilities.js";

  export let params;
  let document_id = params.document_id;

  let document = {};
  let stored_document = {};

  let staff = [];

  let mounted = false;

  onMount(async () => {
    jspa("/Document", "getDocument", { id: document_id })
      .then((result) => {
        document = result.result;

        if (document.collect_expiry == "1") document.collect_expiry = true;
        if (document.collect_expiry == "0") document.collect_expiry = false;

        if (document.collect_completion == "1")
          document.collect_completion = true;
        if (document.collect_completion == "0")
          document.collect_completion = false;
      })
      .finally(() => {
        stored_document = Object.assign({}, document);
      });

    jspa("/Participant/Document", "listParticipantByDocumentId", {
      document_id: document_id,
    }).then((result) => {
      staff = result.result;
    });

    BreadcrumbStore.set({
      path: [{ url: "/documents", name: "Documents" }],
    });

    mounted = true;
  });

  function undo() {
    document = Object.assign({}, stored_document);
  }

  function save() {
    jspa("/Document", "updateDocument", document).then((result) => {
      // Make a copy of the object
      stored_document = Object.assign({}, document);
      push("/documents");
      toastSuccess("Document updated successfully");
    });
  }

  function deleteDocument() {
    jspa("/Document", "deleteDocument", { id: document.id })
      .then((result) => {
        push("/documents");
        toastSuccess("Document deleted");
      })
      .catch((error) => {
        toastError("Error deleting document");
      });
  }

  $: {
    let show = true; //(params.id !=null || !(JSON.stringify(document) === JSON.stringify(stored_document)))
    let can_delete =
      JSON.stringify(document) === JSON.stringify(stored_document) &&
      staff.length == 0;

    if (haveCommonElements($RolesStore, ["super"])) {
      can_delete = true;
    }

    if (mounted) {
      ActionBarStore.set({
        can_delete: can_delete,
        show: show,
        delete: () => deleteDocument(),
        undo: () => undo(),
        save: () => save(),
      });
    }
  }
</script>

<div
  class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
  style="color:#220055;"
>
  {document.name}
</div>
<DocumentForm bind:document />
