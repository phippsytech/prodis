<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import FeedbackForm from "./FeedbackForm.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";

    let feedback = {};

    feedback.status = "open";

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

    function addfeedback() {
        jspa("/Register/Feedback", "addFeedback", feedback)
            .then((result) => {
                let feedback_id = result.result.id;
                push("/registers/feedbacks/" + feedback_id);
            })
            .catch(() => {});
    }
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Add Feedback
</div>

<FeedbackForm bind:feedback />

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addfeedback()} label="Add feedback" />
</div>
