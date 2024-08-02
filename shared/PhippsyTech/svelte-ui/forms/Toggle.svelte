<script>
    import { createEventDispatcher } from "svelte";

    export let value = false;
    export let label_on = null;
    export let label_off = null;
    export let dispatchEvent = false;

    const dispatch = createEventDispatcher();

    function toggle() {
        if (dispatchEvent) {
            dispatch("change");
        } else {
            value = !value;
        }
    }

    $: if (value == 1 || value == true) {
        value = true;
    } else {
        value = false;
    }
</script>

<div class="flex items-center">
    <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
    <button
        on:click={() => toggle()}
        type="button"
        class="{value
            ? 'bg-indigo-600 focus:ring-indigo-600'
            : 'bg-gray-200 focus:ring-transparent'}  relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2"
        role="switch"
        aria-checked="false"
    >
        <span class="sr-only">Use setting</span>
        <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
        <span
            class="{value
                ? 'translate-x-5'
                : 'translate-x-0'} pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
        >
            <!-- Enabled: "opacity-0 duration-100 ease-out", Not Enabled: "opacity-100 duration-200 ease-in" -->
            <span
                class="{value
                    ? 'opacity-0 duration-100 ease-out'
                    : 'opacity-100 duration-200 ease-in'} absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                aria-hidden="true"
            >
                <svg
                    class="h-3 w-3 text-gray-400"
                    fill="none"
                    viewBox="0 0 12 12"
                >
                    <path
                        d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </span>
            <!-- Enabled: "opacity-100 duration-200 ease-in", Not Enabled: "opacity-0 duration-100 ease-out" -->
            <span
                class="{value
                    ? 'opacity-100 duration-200 ease-in'
                    : 'opacity-0 duration-100 ease-out'} absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                aria-hidden="true"
            >
                <svg
                    class="h-3 w-3 text-indigo-600"
                    fill="currentColor"
                    viewBox="0 0 12 12"
                >
                    <path
                        d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"
                    />
                </svg>
            </span>
        </span>
    </button>

    <span class="ml-3 text-sm">
        <span class="font-medium {value ? ' text-gray-900' : 'text-gray-500'}"
            >{#if value}{label_on}{:else}{label_off}{/if}</span
        >
    </span>
</div>
