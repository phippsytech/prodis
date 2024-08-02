<script>
    import { onMount } from "svelte";

    import Container from "@shared/Container.svelte";
    import { PlusIcon } from "heroicons-svelte/24/outline";
    import NoteForm from "./NoteForm.svelte";
    import Role from "@shared/Role.svelte";

    import { jspa } from "@shared/jspa.js";
    import { ModalStore } from "@app/Overlays/stores";
    import { StafferStore } from "@shared/stores.js";
    import { convertToLocalDate } from "@shared/utilities.js";

    export let client_id;

    $: stafferStore = $StafferStore;

    let notes = [];

    let note = {};

    onMount(async () => {
        note = {
            client_id: client_id,
            note: "",
        };
        listNotesByClientId(client_id);
    });

    function listNotesByClientId(client_id) {
        notes = [];
        jspa("/Client/Note", "listNotesByClientId", {
            client_id: client_id,
        }).then((result) => {
            notes = result.result;
        });
    }

    function showNote() {
        // note={
        //     client_id: client_id,
        //     staff_id: stafferStore.id,
        //     note: ""
        // }
        ModalStore.set({
            label: "Add Admin Note",
            show: true,
            props: note,
            component: NoteForm,
            action_label: "Add",
            action: () => addNote(),
        });
    }

    function addNote() {
        jspa("/Client/Note", "addNote", note).then((result) => {
            notes = result.result;
            listNotesByClientId(client_id);
        });
    }
</script>

<!-- <Role roles={["admin"]}> -->
<Container>
    <div class="flex justify-between">
        <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
            Admin Notes
        </h1>
        <Role roles={["participant.modify"]}>
            <div on:click={() => showNote()}><PlusIcon class="w-6 h-6" /></div>
        </Role>
    </div>

    <ul
        role="list"
        class="divide-y divide-gray-100 bg-white px-2 border border-gray-100 rounded-lg"
    >
        {#each notes as note}
            <li class="py-2">
                <div class="flex items-center gap-x-2">
                    <date
                        date={note.created}
                        class="flex-auto text-xs font-semibold text-gray-800"
                        >{convertToLocalDate(note.created)}</date
                    >
                    <h3 class="flex-none text-xs text-gray-400">
                        {note.user_name}
                    </h3>
                </div>
                <p class="text-sm text-gray-600">{note.note}</p>
            </li>
        {/each}
    </ul>
</Container>
<!-- </Role> -->
