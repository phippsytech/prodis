<script>
    // import {StafferStore} from '@shared/stores.js'
    import { push } from "svelte-spa-router";

    // $: stafferStore = $StafferStore;
</script>

<div>
    <button
        on:click={() => push("/profile")}
        type="button"
        class="flex items-center text-sm hover:text-indigo-600
        "
        id="user-menu-button"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <span class="sr-only">User Profile</span>

        <span class="relative inline-block">
            <svg
                class="h-8 w-8"
                fill="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path
                    clip-rule="evenodd"
                    fill-rule="evenodd"
                    d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
                ></path>
            </svg>

            <!-- This to display a message notification icon -->
            <!-- <span class="absolute right-0 top-0 block text-xs w-2 h-2 rounded-full bg-red-400 ring-2 ring-white" ></span> -->
        </span>

        <!-- {#if stafferStore.name}<span class="pl-1 pr-3">{stafferStore.name}</span>{/if} -->
    </button>
</div>
