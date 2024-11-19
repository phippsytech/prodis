<script>
  import { onMount } from "svelte";
  import { push } from "svelte-spa-router";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import Container from "@shared/Container.svelte";
  import MeetingNoteForm from "./MeetingNoteForm.svelte";

  let meeting_note = {};
  let stored_meeting_note = {};
  let mounted = false;

  onMount(async () => {
    BreadcrumbStore.set({
      path: [{ name: "Meeting Notes", url: "/meetingnotes" }],
    });

    mounted = true;
  });

  function undo() {
    meeting_note = Object.assign({}, stored_meeting_note);
  }

  function save() {
    jspa("/MeetingNote", "addMeetingNote", meeting_note).then((result) => {
      push("/meetingnotes/" + result.result.id);
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
    Add Meeting Note
  </div>

  <MeetingNoteForm bind:meeting_note />

  <!-- <p class="text-center opacity-50">You will be able to add documents to this meeting after it is added.</p> -->
</Container>
