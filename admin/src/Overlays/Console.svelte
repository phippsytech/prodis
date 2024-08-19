<script>
    import { consoleVisible, closeConsole, consoleLogs } from "./consoleStore";
    import { onDestroy } from "svelte";

    let isOpen;

    $: logs = $consoleLogs;

    const unsubscribeVisible = consoleVisible.subscribe((value) => {
        isOpen = value;
    });

    onDestroy(() => {
        unsubscribeVisible();
    });
</script>

{#if isOpen}
    <div class="modal">
        <button class="close-button" on:click={closeConsole}>✖</button>
        <div class="console-box">
            {#each logs as log}
                <div class={log.type}>{log.message}</div>
            {/each}
        </div>
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
        padding: 4px;
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
    .log {
        color: #fff;
        padding: 5px;
        margin: 5px 0;
    }
    .log:not([class~="error"]):not([class~="warn"]):not([class~="info"]) {
        border-top: 1px solid #fff;
    }
    .error {
        background: #ff000022;
        padding: 5px;
        margin: 5px 0;
    }
    .warn {
        background: #ffff0022;
        padding: 5px;
        margin: 5px 0;
    }
    .info {
        background: #0000ff22;
        padding: 5px;
        margin: 5px 0;
    }
</style>
