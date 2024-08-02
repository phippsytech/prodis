<script>
  export let content = "";
  export let height = 450;
  export let reformat = false;

  const allowedTags = [
    "ul",
    "ol",
    "li",
    "div",
    "span",
    "sup",
    "p",
    "u",
    "br",
    "b",
    "strong",
    "i",
    "em",
    "blockquote",
    "a",
  ];

  function execCommand(command) {
    document.execCommand(command, false, null);
  }

  function removeAttributes(element) {
    for (const attribute of Array.from(element.attributes)) {
      if (attribute.name === "class" || attribute.name === "style") {
        element.removeAttribute(attribute.name);
      }
    }

    for (const child of Array.from(element.children)) {
      removeAttributes(child);
    }

    if (
      element.tagName.toLowerCase() !== "br" &&
      element.tagName.toLowerCase() !== "p" &&
      element.textContent.trim() === ""
    ) {
      if (element.parentNode) element.parentNode.removeChild(element);
      return;
    }
  }

  function sanitizeInput(event) {
    if (event.inputType === "insertFromPaste") {
      let sanitizedHTML = event.dataTransfer.getData("text/html");
      if (sanitizedHTML == "") {
        sanitizedHTML = event.dataTransfer.getData("text/plain");
      }
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = sanitizedHTML;

      for (const child of Array.from(tempDiv.querySelectorAll("*"))) {
        if (!allowedTags.includes(child.tagName.toLowerCase())) {
          if (child.parentNode) {
            child.parentNode.removeChild(child);
          }
        } else {
          removeAttributes(child);
        }
      }

      event.preventDefault();
      document.execCommand("insertHTML", false, tempDiv.innerHTML);
    }
  }

  function clean() {
    content = content.replace(/\n/g, "<p>");
  }
</script>

<nav
  class="bg-white text-slate-300 border border-indigo-100 border-b-0 rounded-t-lg drop-shadow-sm flex space-x-2 items-center px-2 py-1"
  aria-label="Toolbar"
>
  <button
    on:click={() => execCommand("bold")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Bold</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 384 512"
      ><path
        d="M0 64C0 46.3 14.3 32 32 32H80 96 224c70.7 0 128 57.3 128 128c0 31.3-11.3 60.1-30 82.3c37.1 22.4 62 63.1 62 109.7c0 70.7-57.3 128-128 128H96 80 32c-17.7 0-32-14.3-32-32s14.3-32 32-32H48V256 96H32C14.3 96 0 81.7 0 64zM224 224c35.3 0 64-28.7 64-64s-28.7-64-64-64H112V224H224zM112 288V416H256c35.3 0 64-28.7 64-64s-28.7-64-64-64H224 112z"
      /></svg
    >
  </button>

  <button
    on:click={() => execCommand("italic")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Italic</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 384 512"
      ><path
        d="M128 64c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32s-14.3 32-32 32H293.3L160 416h64c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H90.7L224 96H160c-17.7 0-32-14.3-32-32z"
      /></svg
    >
  </button>

  <button
    on:click={() => execCommand("justifyleft")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Align Left</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 448 512"
      ><path
        d="M288 64c0 17.7-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32H256c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"
      /></svg
    >
  </button>

  <button
    on:click={() => execCommand("justifycenter")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Align Centre</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 448 512"
      ><path
        d="M352 64c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32zm96 128c0-17.7-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32zM0 448c0 17.7 14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H32c-17.7 0-32 14.3-32 32zM352 320c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32H320c17.7 0 32-14.3 32-32z"
      /></svg
    >
  </button>

  <button
    on:click={() => execCommand("justifyright")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Align Right</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 448 512"
      ><path
        d="M448 64c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"
      /></svg
    >
  </button>

  <button
    on:click={() => execCommand("insertunorderedlist")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Bullet List</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 512 512"
      ><path
        d="M64 144a48 48 0 1 0 0-96 48 48 0 1 0 0 96zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM64 464a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm48-208a48 48 0 1 0 -96 0 48 48 0 1 0 96 0z"
      /></svg
    >
  </button>

  <button
    on:click={() => execCommand("insertorderedlist")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Number List</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 512 512"
      ><path
        d="M24 56c0-13.3 10.7-24 24-24H80c13.3 0 24 10.7 24 24V176h16c13.3 0 24 10.7 24 24s-10.7 24-24 24H40c-13.3 0-24-10.7-24-24s10.7-24 24-24H56V80H48C34.7 80 24 69.3 24 56zM86.7 341.2c-6.5-7.4-18.3-6.9-24 1.2L51.5 357.9c-7.7 10.8-22.7 13.3-33.5 5.6s-13.3-22.7-5.6-33.5l11.1-15.6c23.7-33.2 72.3-35.6 99.2-4.9c21.3 24.4 20.8 60.9-1.1 84.7L86.8 432H120c13.3 0 24 10.7 24 24s-10.7 24-24 24H32c-9.5 0-18.2-5.6-22-14.4s-2.1-18.9 4.3-25.9l72-78c5.3-5.8 5.4-14.6 .3-20.5zM224 64H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 160H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 160H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32s14.3-32 32-32z"
      /></svg
    >
  </button>

  <button
    on:click={() => execCommand("indent")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Indent</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 448 512"
      ><path
        d="M0 64C0 46.3 14.3 32 32 32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64zM192 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32zm32 96H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32s14.3-32 32-32zM0 448c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM127.8 268.6L25.8 347.9C15.3 356.1 0 348.6 0 335.3V176.7c0-13.3 15.3-20.8 25.8-12.6l101.9 79.3c8.2 6.4 8.2 18.9 0 25.3z"
      /></svg
    >
  </button>

  <button
    on:click={() => execCommand("outdent")}
    class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
  >
    <span class="sr-only">Outdent</span>
    <svg
      fill="currentColor"
      xmlns="http://www.w3.org/2000/svg"
      height="1em"
      viewBox="0 0 448 512"
      ><path
        d="M0 64C0 46.3 14.3 32 32 32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64zM192 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32zm32 96H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32s14.3-32 32-32zM0 448c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM.2 268.6c-8.2-6.4-8.2-18.9 0-25.3l101.9-79.3c10.5-8.2 25.8-.7 25.8 12.6V335.3c0 13.3-15.3 20.8-25.8 12.6L.2 268.6z"
      /></svg
    >
  </button>

  {#if reformat}
    <button
      on:click={() => clean()}
      class="h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-600 hover:text-white"
    >
      <span class="text-sm px-2">Reformat Case Note</span>
    </button>
  {/if}
</nav>

<div
  class="editor min-h-400 overflow-y-auto rounded-b-lg border border-indigo-100 border-t-0 p-2 outline-none bg-white shadow-sm"
  contenteditable="true"
  bind:innerHTML={content}
  on:beforeinput={sanitizeInput}
  style={`max-height: ${height}px; min-height: ${height}px;`}
></div>

<style>
  :global(.editor) {
    padding: 10px;
    /* max-height: 450px;
      min-height: 450px; */
  }

  :global(.editor b, .editor strong) {
    font-weight: bold;
  }

  :global(.editor i, .editor em) {
    font-style: italic;
  }

  :global(.editor ul, .editor ol) {
    /* list-style: initial; 
    margin: initial;       */
    margin-left: 0.5em;
  }

  :global(.editor p, .editor div) {
    margin-bottom: 1em;
  }

  :global(.editor ul li, .editor ol li) {
    /* margin-bottom: 0.5em; */
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

  :global(.toolbar) {
    margin-top: 10px;
  }
</style>
