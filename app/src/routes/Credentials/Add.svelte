<script>
  import { onMount } from "svelte";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import CredentialForm from "./CredentialForm.svelte";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { push } from "svelte-spa-router";

  let credential = {
    id: null,
    name: "",
    collect_expiry: true,
  };
  let stored_credential = {};

  let mounted = false;

  onMount(async () => {
    BreadcrumbStore.set({
      path: [{ url: "/credentials", name: "Credentials" }],
    });
    stored_credential = Object.assign({}, credential);
    mounted = true;
  });

  function undo() {
    credential = Object.assign({}, stored_credential);
  }

  function save() {
    jspa("/Credential", "addCredential", credential).then((result) => {
      // Make a copy of the object
      stored_credential = Object.assign({}, credential);
      push("/credentials");
      toastSuccess("Credential updated successfully");
    });
  }

  $: {
    if (mounted) {
      ActionBarStore.set({
        can_delete: false,
        show: !(
          JSON.stringify(credential) === JSON.stringify(stored_credential)
        ),
        undo: () => undo(),
        save: () => save(),
      });
    }
  }
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Add Credential
</div>
<CredentialForm bind:credential />
