<script>
    import { onMount } from "svelte";

    let displayMode = "light";

    onMount(async () => {
        displayMode = localStorage.displayMode || "light";
        setDisplayMode(displayMode);
    });

    function toggleInvert() {
        if (displayMode === "light") {
            displayMode = "dark";
        } else {
            displayMode = "light";
        }

        localStorage.setItem("displayMode", displayMode);
        setDisplayMode(displayMode);
    }

    function setDisplayMode(displayMode) {
        const style = document.documentElement.style;
        if (displayMode === "light") {
            style.webkitFilter = "";
        } else {
            style.webkitFilter =
                "invert() hue-rotate(190deg) brightness(120%) contrast(105%)";
        }
    }
</script>

<button class="pl-4" on:click={() => toggleInvert()}>
    {#if displayMode == "dark"}
        <svg
            class="h-6 w-6 text-gray-400"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"
            ></path>
        </svg>
    {:else}
        <svg
            class="h-6 w-6 text-gray-400"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
            ></path>
        </svg>
    {/if}
</button>
