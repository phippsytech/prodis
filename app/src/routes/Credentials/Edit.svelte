<script>
  import { onMount } from "svelte";
  import { RolesStore, BreadcrumbStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import CredentialForm from "./CredentialForm.svelte";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { push } from "svelte-spa-router";

  import { haveCommonElements } from "@shared/utilities.js";

  export let params;
  let credential_id = params.credential_id;

  let credential = {};
  let stored_credential = {};

  let staff = [];

  let mounted = false;

  onMount(async () => {
    jspa("/Credential", "getCredential", { id: credential_id })
      .then((result) => {
        credential = result.result;

        if (credential.collect_expiry == "1") credential.collect_expiry = true;
        if (credential.collect_expiry == "0") credential.collect_expiry = false;

        if (credential.collect_completion == "1")
          credential.collect_completion = true;
        if (credential.collect_completion == "0")
          credential.collect_completion = false;
      })
      .finally(() => {
        stored_credential = Object.assign({}, credential);
      });

    jspa("/Staff/Credential", "listStaffByCredentialId", {
      credential_id: credential_id,
    }).then((result) => {
      staff = result.result;
    });

    BreadcrumbStore.set({
      path: [{ url: "/credentials", name: "Credentials" }],
    });

    mounted = true;
  });

  function undo() {
    credential = Object.assign({}, stored_credential);
  }

  function save() {
    jspa("/Credential", "updateCredential", credential).then((result) => {
      // Make a copy of the object
      stored_credential = Object.assign({}, credential);
      push("/credentials");
      toastSuccess("Credential updated successfully");
    });
  }

  function deleteCredential() {
    jspa("/Credential", "deleteCredential", { id: credential.id })
      .then((result) => {
        push("/credentials");
        toastSuccess("Credential deleted");
      })
      .catch((error) => {
        toastError("Error deleting credential");
      });
  }

  $: {
    let show = true; //(params.id !=null || !(JSON.stringify(credential) === JSON.stringify(stored_credential)))
    let can_delete =
      JSON.stringify(credential) === JSON.stringify(stored_credential) &&
      staff.length == 0;

    if (haveCommonElements($RolesStore, ["super"])) {
      can_delete = true;
    }

    if (mounted) {
      ActionBarStore.set({
        can_delete: can_delete,
        show: show,
        delete: () => deleteCredential(),
        undo: () => undo(),
        save: () => save(),
      });
    }
  }
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  {credential.name}
</div>
<CredentialForm bind:credential />
