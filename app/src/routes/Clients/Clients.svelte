<script>
    import Role from "@shared/Role.svelte";
    import List from "./List.svelte";
    import JSON2CSV from "@shared/JSON2CSV.svelte";
    import { push } from "svelte-spa-router";
    import { TabStore, BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";

    export let search = "";

    TabStore.set({ tabs: [], selected: null });

    document.title = "Clients";
    BreadcrumbStore.set({ path: [{ name: "Clients" }] });

    let participantMailingList = [];

    jspa("/Client", "getClientMailingList", {}).then((result) => {
        participantMailingList = result.result;
    });
</script>

<Role roles={["admin"]}>
    <JSON2CSV
        filename="participant-list.csv"
        bind:json_data={participantMailingList}
        label="Participant List CSV"
    />
</Role>
<div class="bg-white px-4 py-4 mb-4 mt-2">
    <!--   
    <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight px-2 mt-0 mb-2">Clients</h2> -->

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
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
                placeholder="Search by client name / ndis number ..."
                type="search"
                name="search"
            />
        </div>

        <Role roles={["admin"]}>
            <div class="flex items-center gap-x-4 lg:gap-x-6">
                <button
                    on:click={() => push("/clients/add")}
                    type="button"
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >Add Client</button
                >
            </div>
        </Role>
    </div>
</div>

<List bind:search />
