<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import { jspa } from "@shared/jspa.js";
    export let task_id;

    let comment = {
        task_id: task_id,
        content: "",
    };

    function addComment(comment) {
        jspa("/Task", "addComment", comment).then((result) => {
            comment = result.result;
        });
    }
</script>

<div>
    <RTE height="250" bind:content={comment.content} />
</div>
<div class="mt-2 flex items-center justify-end space-x-2">
    <button
        on:click={() => addComment(comment)}
        class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
        >Add Comment</button
    >
</div>
