<script>
    import { push } from "svelte-spa-router";
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import FileUploader from "@shared/FileUploader.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import { SpinnerStore } from "@shared/Overlays/stores.js";
    import { CloudArrowUpIcon } from "heroicons-svelte/24/outline";
    import { jspa } from "@shared/jspa.js";

    export let params;

    let meeting_note = {};
    let document = {};
    let breadcrumbs_path = [];

    BreadcrumbStore.set({
        path: [{ name: "Meeting Notes", url: "/meetingnotes" }],
    });

    jspa("/MeetingNote", "getMeetingNote", { id: params.id }).then((result) => {
        meeting_note = result.result;

        breadcrumbs_path = [
            { url: "/meetingnotes", name: "Meeting Notes" },
            { url: "/meetingnotes/" + params.id, name: meeting_note.purpose },
        ];
    });

    function upload() {
        SpinnerStore.set({ show: true, message: "Uploading File" });

        document.client_id = params.id;
        jspa(
            "/MeetingNote/Document",
            "uploadMeetingNoteDocument",
            document,
        ).then((result) => {
            SpinnerStore.set({ show: false });
            push("/meetingnotes/" + params.id);
        });
    }
</script>

<Container>
    <FloatingInput
        bind:value={document.name}
        label="Document Name"
        placeholder="eg: Cert 4 in Disability support"
    />

    <FileUploader bind:value={document.base64_file} />

    {#if document.base64_file}
        <div class="flex justify-between">
            <span></span>
            <button
                on:click={() => upload()}
                type="button"
                class="inline-block px-6 py-4 bg-blue-600 text-white font-medium text-sm leading-snug uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                ><CloudArrowUpIcon class="w-5 h-5 inline" /> Upload</button
            >
        </div>
    {/if}
</Container>
