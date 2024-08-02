<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { StafferStore, BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { getClient } from "@shared/api.js";
    import { formatDate } from "@shared/utilities.js";

    import RTE from "@shared/RTE/RTE.svelte";
    import { push } from "svelte-spa-router";

    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    $: stafferStore = $StafferStore;

    let client = {};

    export let params;
    let client_id = params.client_id;

    let mounted = false;

    let casenote = {
        client_id: client_id,
        notes: "",
        internal: false,
        // staff_id: stafferStore,
    };

    let stored_casenote = {};

    onMount(async () => {
        client = await getClient(client_id);

        BreadcrumbStore.set({
            path: [
                { name: "Clients", url: "/clients" },
                { name: client.name, url: "/clients/" + client.id },
            ],
        });

        stored_casenote = Object.assign({}, casenote);

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
        casenote.staff_id = stafferStore.id;
        jspa("/Client/CaseNote", "addCaseNote", casenote).then((result) => {
            stored_casenote = Object.assign({}, casenote);
            push("/clients/" + client.id + "/casenotes");
            toastSuccess("Case Note Added");
        });
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(casenote) === JSON.stringify(stored_casenote)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }

    function formatUnixTime(unixTime) {
        if (Number.isInteger(unixTime) && unixTime >= 0) {
            return new Date(unixTime * 1000).toISOString().split("T")[0];
        }
    }
</script>

<div class="">
    <div
        class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular px-4"
        style="color:#220055;"
    >
        Add Case Note for {client.name}
    </div>
    <p class="text-xl font-light px-4">
        {formatDate(formatUnixTime(casenote.created))}
    </p>
    <!-- <p class="text-xl font-light px-4">{formatUnixTime(casenote.created)}</p> -->

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
