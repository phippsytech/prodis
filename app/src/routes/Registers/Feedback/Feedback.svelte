<script>
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import FeedbackForm from "./FeedbackForm.svelte";

  export let params;

  let feedback = {
    status: "open",
    type: "compliment",
    name: null,
    email: null,
    phone: null,
    message: null,
    resolution: null,
  };
  let stored_feedback = Object.assign({}, feedback);
  let mounted = false;
  let readOnly = false;

  BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

  onMount(() => {
    if (params.id != "add") {
      jspa("/Register/Feedback", "getFeedback", { id: params.id })
        .then((result) => {
          feedback = result.result;
        })
        .catch(() => {})
        .finally(() => {
          // Make a copy of the object
          stored_feedback = Object.assign({}, feedback);
        });
    }
    mounted = true;
  });

  function undo() {
    feedback = Object.assign({}, stored_feedback);
  }

  function save() {
    jspa("/Register/Feedback", "updateFeedback", feedback)
      .then((result) => {
        feedback = result.result;
        stored_feedback = Object.assign({}, feedback);
      })
      .catch(() => {});
  }

  $: {
    if (mounted) {
      ActionBarStore.set({
        can_delete: false,
        show: !(JSON.stringify(feedback) === JSON.stringify(stored_feedback)),
        undo: () => undo(),
        save: () => save(),
      });
    }
  }

  $: {
    readOnly = feedback.status == "closed";
  }
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Add Feedback
</div>

<FeedbackForm bind:feedback bind:readOnly />
