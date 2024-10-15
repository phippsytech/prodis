<script>
  import { onMount } from "svelte";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import DocumentForm from "./DocumentForm.svelte";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { push } from "svelte-spa-router";

  let document = {
    id: null,
    name: "",
    collect_expiry: true,
  };
  let stored_document = {};

  let selectedParticipant = [];

  let mounted = false;

  onMount(async () => {
    BreadcrumbStore.set({
      path: [{ url: "/documents", name: "Documents" }],
    });
    stored_document = Object.assign({}, document);
    mounted = true;
  });

  function undo() {
    document = Object.assign({}, stored_document);
  }

  function save() {
    jspa("/Document", "addDocument", document).then((result) => {
      console.log(selectedParticipant);
      // Make a copy of the object
      stored_document = Object.assign({}, document);
      push("/documents");
      toastSuccess("Document updated successfully");
    });
  }

  $: {
    if (mounted) {
      ActionBarStore.set({
        can_delete: false,
        show: !(JSON.stringify(document) === JSON.stringify(stored_document)),
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
  Add Document
</div>
<DocumentForm bind:document {selectedParticipant}  />
