<script>
    import List from "./List.svelte";
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";

    BreadcrumbStore.set({ path: [{ name: "Services" }] });

    let search = "";
</script>

<div class="flex grow flex-col gap-4 mt-2">
    <div class="flex h-16 shrink-0 items-center bg-white rounded-md">
        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-2 px-3">
            <div class="relative flex flex-1">
                <label for="search-field" class="sr-only">Search</label>
                <svg
                    class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd"
                    />
                </svg>
                <input
                    bind:value={search}
                    id="search-field"
                    class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                    placeholder="search by staff name..."
                    type="search"
                    name="search"
                />
            </div>

            <div class="flex items-center gap-x-4 lg:gap-x-6">
                <button
                    on:click={() => push("/services/add")}
                    type="button"
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >Add Service</button
                >
            </div>
        </div>
    </div>
    <div class="mt-4">
        <List bind:search />
    </div>
</div>
