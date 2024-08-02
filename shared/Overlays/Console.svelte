<script>
    import { onMount } from "svelte";
    import { consoleVisible, closeConsole } from "./stores";
    import { onDestroy } from "svelte";

    let consoleContainer;
    let isOpen;

    const originalConsole = {
        log: console.log,
        error: console.error,
        warn: console.warn,
        info: console.info,
    };

    function appendMessage(type, args) {
        const message = document.createElement("div");
        message.classList.add(type);
        message.textContent = Array.from(args).join(" ");
        consoleContainer.appendChild(message);
        consoleContainer.scrollTop = consoleContainer.scrollHeight; // Scroll to the bottom
    }

    function overrideConsole() {
        console.log = function (...args) {
            appendMessage("log", args);
            originalConsole.log.apply(console, args);
        };

        console.error = function (...args) {
            appendMessage("error", args);
            originalConsole.error.apply(console, args);
        };

        console.warn = function (...args) {
            appendMessage("warn", args);
            originalConsole.warn.apply(console, args);
        };

        console.info = function (...args) {
            appendMessage("info", args);
            originalConsole.info.apply(console, args);
        };
    }

    onMount(() => {
        overrideConsole();
    });

    const unsubscribe = consoleVisible.subscribe((value) => {
        isOpen = value;
    });

    onDestroy(() => {
        unsubscribe();
    });
</script>

{#if isOpen}
    <div class="modal">
        <button class="close-button" on:click={closeConsole}>✖</button>
        <div class="console-box" bind:this={consoleContainer}></div>
    </div>
{/if}

<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.85);
        z-index: 1000;
    }
    .console-box {
        width: 100%;
        height: 100%;
        background: #333;
        color: #fff;
        padding: 10px;
        box-sizing: border-box;
        font-family: monospace;
        overflow-y: auto;
        white-space: pre-wrap; /* This ensures that long lines will wrap */
        word-break: break-all; /* This forces wrapping for long words with no spaces */
    }
    .close-button {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        border: none;
        color: #fff;
        font-size: 1.5em;
        cursor: pointer;
    }
    .log:not([class~="error"]):not([class~="warn"]):not([class~="info"]) {
        color: #fff;
        border-top: 1px solid #fff;
    }
    .error {
        color: #f00;
    }
    .warn {
        color: #ff0;
    }
    .info {
        color: #00f;
    }
</style>
