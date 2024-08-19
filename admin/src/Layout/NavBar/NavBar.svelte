<script>
    import UserWidget from "./UserWidget.svelte";
    import { onMount, beforeUpdate } from "svelte";
    import Role from "@shared/Role.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";

    $: path = $BreadcrumbStore.path || [];

    onMount(async () => {
        window.addEventListener("popstate", () =>
            BreadcrumbStore.set({ path: [] }),
        );
    });

    let pathname = window.location.hash;
    let segments = pathname.split("/");
    let area = segments[1];
    let selected = "/#/" + area;

    let hostname = window.location.hostname;
    let is_development = false;
    if (hostname.includes("phippsy.tech")) {
        is_development = true;
    }

    const updateHashValue = () => {
        pathname = window.location.hash;
        segments = pathname.split("/");
        area = segments[1];
        selected = "/#/" + area;
    };

    onMount(() => {
        updateHashValue();
        window.addEventListener("hashchange", updateHashValue);

        return () => {
            window.removeEventListener("hashchange", updateHashValue);
        };
    });

    beforeUpdate(() => {
        updateHashValue();
    });

    function reload() {
        SpinnerStore.set({ show: true, message: "Reloading" });
        location.reload();
    }
</script>

<nav
    class="print:hidden fixed bg-indigo w-full z-20"
>
    <div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8">
        <div class="flex h-12 justify-between">
            <div class="flex px-2 lg:px-0">
                <div class=" flex space-x-8">
                    {#if pathname == "" || pathname == "#/"}
                        <div class="flex items-center space-x-2 ml-2">
                            <div class=" font-bold text-white">
                                Prodis Admin
                            </div>
                        </div>
                    {:else}
                        <ol role="list" class="flex items-center space-x-1">
                            <li>
                                <a
                                    href="/#/"
                                    class=" text-indigo-600 hover:text-indigo-500"
                                >
                                    <svg
                                        class="h-5 w-5 flex-shrink-0"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>

                                    <span class="sr-only">Home</span>
                                </a>
                            </li>
                            {#if path.length > 0}
                                {#each path as item}
                                    <li>
                                        <div class="flex items-center">
                                            <svg
                                                class="h-5 w-5 flex-shrink-0 text-indigo-100"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            {#if item.url == null}
                                                <span
                                                    class="ml-1 text-sm font-medium text-slate-500 hover:text-slate-700"
                                                    aria-current="page"
                                                    >{item.name}</span
                                                >
                                            {:else}
                                                <a
                                                    href="/#{item.url}"
                                                    class="ml-1 text-sm font-medium text-slate-500 hover:text-slate-700"
                                                    >{item.name}</a
                                                >
                                            {/if}
                                        </div>
                                    </li>
                                {/each}
                            {/if}
                        </ol>
                    {/if}
                </div>
            </div>

            <div class="block p-2">
                <div class=" ml-4 flex items-center text-slate-800">
                    <Role roles={["therapist"]}>
                        <a
                            href="/#/notifications"
                            class="block relative ml-4 flex-shrink-0 cursor-pointer"
                        >
                            <svg
                                class="h-6 w-6 text-slate-200 hover:text-indigo-600"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"
                                ></path>
                            </svg>
                        </a>
                    </Role>

                    <div class="hidden lg:block relative ml-4 flex-shrink-0">
                        <UserWidget />
                    </div>

                    <div
                        on:click={() => reload()}
                        class="block relative ml-4 flex-shrink-0 cursor-pointer"
                    >
                        <svg
                            class="h-6 w-6 hover:text-indigo-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            ></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="h-10" />

<style>
    .rainbow-box {
        border-top: 8px solid transparent; /* Transparent borders */
    }

    .rainbow-box::before {
        content: "";
        position: absolute;
        top: -8px; /* Adjusted to sit on top of the box */
        left: 0;
        right: 0;
        height: 16px; /* Height of the top border */
        background-image: linear-gradient(
            to right,
            #ff0000 0%,
            #ff4500 10%,
            #ff6347 20%,
            #ff7f50 30%,
            #ff8c00 40%,
            #ffa500 50%,
            #ff8c00 60%,
            #ff7f50 70%,
            #ff6347 80%,
            #ff4500 90%,
            #ff0000 100%
        );
        background-size: 200% 100%; /* Enlarge background to allow smooth transition */
        animation: slide-gradient 60s linear infinite; /* Apply the animation */
    }

    /* Original Purple-Blue Gradient Cycle */
    /* background-image: linear-gradient(
            to right,
            #9333ea 0%,
            #8b5cf6 10%,
            #4f46e5 20%,
            #2563eb 30%,
            #0ea5e9 40%,
            #0891b2 50%,
            #0ea5e9 60%,
            #2563eb 70%,
            #4f46e5 80%,
            #8b5cf6 90%,
            #9333ea 100%
        ); */

    @keyframes slide-gradient {
        from {
            background-position: -100% 0;
        }
        to {
            background-position: 100% 0;
        }
    }
</style>
