<script>
    import { pop } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ArrowLeftIcon } from "heroicons-svelte/24/outline";

    BreadcrumbStore.set({
        path: [],
    });
</script>

<div class="md:container mx-auto p-3" style="height:80vh">
    <div class="grid items-center h-full">
        <div class="px-6 md:px-12 mx-auto">
            <span class="text-indigo-600">404 Error</span>
            <h1
                class="text-indigo-600 md:text-7xl text-5xl font-bold mt-0 mb-2 mb-8"
            >
                Not Found
            </h1>
            <div class="flex flex-row justify-end items-center">
                <button
                    on:click={() => pop()}
                    type="button"
                    class="block rounded-md px-3 py-2 text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    <ArrowLeftIcon class="h-5 w-5 inline" /> Go Back
                </button>
            </div>
        </div>
    </div>
</div>
