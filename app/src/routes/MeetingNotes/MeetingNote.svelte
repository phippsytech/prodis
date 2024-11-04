<script>
  import { onMount } from "svelte";
  import { push } from "svelte-spa-router";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { jspa } from "@shared/jspa.js";
  import Container from "@shared/Container.svelte";
  import MeetingNoteForm from "./MeetingNoteForm.svelte";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";

  export let params = {};

  let meeting_note_id = params.meetingnote_id;
  let meeting_note = {};
  let stored_meeting_note = {};
  let mounted = false;

  onMount(() => {
    jspa("/MeetingNote", "getMeetingNote", { id: meeting_note_id })
      .then((result) => {
        meeting_note = result.result;

        jspa("/MeetingNote/Document", "listMeetingNoteDocuments", {
          meeting_note_id: meeting_note_id,
        })
          .then((result) => {
            meeting_note.documents = result.result;
          })
          .finally(() => {
            // Make a copy of the object
            stored_meeting_note = Object.assign({}, meeting_note);
          });
      })
      .catch((error) => {});

    BreadcrumbStore.set({
      path: [{ name: "Meeting Notes", url: "/meetingnotes" }],
    });

    mounted = true;
  });

  // handles the delete event called from the DcocumentList component
  function deleteDocument(event) {
    let document_id = event.detail.document_id;

    meeting_note.documents.forEach((document, index) => {
      if (document.id == document_id) {
        meeting_note.documents[index].status = "deleting";
      }
    });

    let document = {
      id: event.detail.document_id,
      meeting_note_id: meeting_note_id,
    };
    jspa("/MeetingNote/Document", "deleteMeetingNoteDocument", document).then(
      (result) => {
        meeting_note.documents = meeting_note.documents.filter(
          (document) => document.id !== event.detail.document_id
        );
        // Make a copy of the object
        stored_meeting_note = Object.assign({}, meeting_note);
        toastSuccess("Document deleted");
      }
    );
  }

  function undo() {
    meeting_note = Object.assign({}, stored_meeting_note);
  }

  function save() {
    jspa("/MeetingNote", "updateMeetingNote", meeting_note).then((result) => {
      push("/meetingnotes");
      toastSuccess("Meeting Notes Updated");
    });
  }

  $: {
    if (mounted) {
      ActionBarStore.set({
        can_delete: false,
        show: !(
          JSON.stringify(meeting_note) === JSON.stringify(stored_meeting_note)
        ),
        undo: () => undo(),
        save: () => save(),
      });
    }
  }
</script>

<Container>
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Edit Meeting Note
  </div>

  <MeetingNoteForm bind:meeting_note />

  <!-- <div class="flex justify-between ">
            <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Documents</h1>
            <div on:click={()=>push('/meetingnotes/'+meeting_note.id+'/adddocument')}><DocumentAddIcon  class="w-6 h-6"  /></div>
        </div>
        <DocumentList documents={meeting_note.documents} on:delete={deleteDocument} />
     -->
</Container>
