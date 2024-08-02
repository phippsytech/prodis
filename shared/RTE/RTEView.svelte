<script>
    export let content = '';
</script>

<div class="editor min-h-400 overflow-y-auto rounded-lg border border-gray-200  p-2 outline-none bg-white" >
    {@html content}
</div>

<style>

    :global(.editor) {
        padding: 10px;
    } 

    :global(.editor b, .editor strong) {
        font-weight: bold;
    }

    :global(.editor i, .editor em) {
        font-style: italic;
    }

    :global(.editor ul, .editor ol ) { 
        margin-left: 0.5em; 
    } 

    :global(.editor p, .editor div ) { 
        margin-bottom: 1em; 
    }   

    :global(.editor ul li:before) {
        content: "•";
        margin-right: 0.5em;
    }

    :global(.editor ol li) {
        counter-increment: list-counter;
    }

    :global(.editor ol li:before) {
        content: counter(list-counter) ".";
        margin-right: 0.5em;
    }

    :global(.editor blockquote) {
        margin-left: 1em;
        border-left: 2px solid #ccc;
        padding-left: 1em;
    }
    
</style>