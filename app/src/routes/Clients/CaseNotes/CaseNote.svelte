<script>
  import { onMount, onDestroy } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { ClientStore, BreadcrumbStore } from "@shared/stores.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { getClient } from "@shared/api.js";
  import { formatDate, convertToLocalDate } from "@shared/utilities.js";
  import Container from "@shared/Container.svelte";
  import RTE from "@shared/RTE/RTE.svelte";
  import RTEView from "@shared/RTE/RTEView.svelte";
  import Role from "@shared/Role.svelte";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { push } from "svelte-spa-router";

  let client = {};

  export let params;
  let client_id = params.client_id;

  let casenote_id = params.casenote_id;

  let mounted = false;

  let casenote = {};

  let stored_casenote = {};

  onMount(async () => {
    client = await getClient(client_id);

    BreadcrumbStore.set({
      path: [
        { url: "/clients", name: "Clients" },
        { name: client.name, url: "/clients/" + client.id },
        { name: "Case Notes", url: "/clients/" + client.id + "/casenotes" },
      ],
    });

    await jspa("/Client/CaseNote", "getCaseNote", { id: casenote_id }).then(
      (result) => {
        casenote = result.result;

        if (casenote.internal == "1") casenote.internal = true;
        if (casenote.internal == "0") casenote.internal = false;

        // casenote.internal = true;
        // casenote.notes = casenote.notes.replace(/\n/g, '<br>');
        stored_casenote = Object.assign({}, casenote);
      }
    );

    mounted = true;
  });

  function copyToClipboard(text) {
    // create a temporary textarea element
    const textarea = document.createElement("textarea");
    // set the text content to be copied to the clipboard
    textarea.value = text;
    // append the textarea element to the document body
    document.body.appendChild(textarea);
    // select the text in the textarea
    textarea.select();
    // copy the selected text to the clipboard
    document.execCommand("copy");
    // remove the temporary textarea element
    document.body.removeChild(textarea);
  }

  function undo() {
    casenote = Object.assign({}, stored_casenote);
  }

  function save() {
    jspa("/Client/CaseNote", "updateCaseNote", casenote).then((result) => {
      stored_casenote = Object.assign({}, casenote);
      push("/clients/" + client.id + "/casenotes");
      toastSuccess("Case Note saved");
    });
  }

  $: {
    if (mounted) {
      ActionBarStore.set({
        can_delete: false,
        show: !(JSON.stringify(casenote) === JSON.stringify(stored_casenote)),
        undo: () => undo(),
        save: () => save(),
      });
    }
  }

  function formatUnixTime(unixTime) {
    return new Date(unixTime * 1000)
      .toLocaleDateString(undefined, {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
      })
      .split("/")
      .reverse()
      .join("-");
  }
</script>

<div class="">
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    {client.name}: {convertToLocalDate(casenote.created)}
  </div>

  <p class="text-sm text-gray-500 px-4">by {casenote.staff_name}</p>

  <Role roles={["therapist", "admin"]} conditional={true}>
    <div slot="authorised">
      <div class="relative flex items-start px-4 mb-2">
        <div class="flex h-6 items-center">
          <input
            id="comments"
            aria-describedby="comments-description"
            name="comments"
            type="checkbox"
            bind:checked={casenote.internal}
            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
          />
        </div>
        <div class="ml-2 text-sm leading-6">
          <label for="comments" class="font-medium text-gray-900"
            >Internal Only</label
          >
          <span id="comments-description" class="text-gray-500 text-xs italic"
            ><span class="sr-only">Internal Only </span> (Tick to prevent stakeholders
            reading this case note)</span
          >
        </div>
      </div>

      <RTE bind:content={casenote.notes} reformat={true} />
    </div>

    <div slot="declined" class="p-4">
      <RTEView bind:content={casenote.notes} />
    </div>
  </Role>
</div>
