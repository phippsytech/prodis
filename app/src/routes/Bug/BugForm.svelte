<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    import RTE from "@shared/RTE/RTE.svelte";

    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    let mounted = false;

    let bug = {
        notes: "",
        internal: false,
        // staff_id: stafferStore,
    };

    let stored_bug = {};

    onMount(async () => {
        BreadcrumbStore.set({
            path: [{ name: "Report a Bug" }],
        });

        stored_bug = Object.assign({}, bug);

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
        bug = Object.assign({}, stored_bug);
    }

    function sendBugReport() {
        bug.staff_id = stafferStore.id;
        jspa("/Bug", "addBug", bug).then((result) => {
            stored_bug = Object.assign({}, bug);

            toastSuccess("Bug Reported");
        });
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(JSON.stringify(bug) === JSON.stringify(stored_bug)),
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

    bug.notes = `
<p>
    <b>Detailed Description:</b><br/>
I am unable to report a bug in the system.  I can enter my bug.  But when I press save, nothing happens.
</p>
<p>
<b>Steps to Reproduce:</b>
<ol>
    <li>I go to the 'report a bug' page</li>
    <li>I describe my issue in detail</li>
    <li>I press save.</li>
      <li>Nothing happens.</li>
</ol>
</p>
<p>
<b>Expected Outcome:</b><br/>
I should see that my bug has been saved.
</p>
`;
</script>

<div class="">
    <div
        class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular mt-2 mx-2"
    >
        Report a Bug
    </div>
    <div class="mb-4 mx-2">
        Use this form to report any issues you encounter using this system.<br
        />
        A sample bug report has been provided for you to use as a template.
    </div>
    <!-- <div class="text-xs mb-4 opacity-50">
        Bugs are annoying, but this is not for complaints. Be kind. Be specific.
        Be helpful.
    </div> -->

    <RTE bind:content={bug.notes} />

    <div class="flex justify-end items-center mb-4">
        <button
            on:click={() => sendBugReport()}
            type="button"
            class="mt-2 block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Report Bug</button
        >
    </div>
</div>
