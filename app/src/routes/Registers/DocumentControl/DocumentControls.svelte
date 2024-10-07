<script>
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatDate } from "@shared/utilities.js";

    let documentcontrols = {};

    jspa("/Register/DocumentControl", "listDocumentControls", {}).then((result) => {
        documentcontrols = result.result;
        // sort the compliments reverse chronologically
        documentcontrols.sort(function (a, b) {
            let aDateTime = Date.parse(a.date);
            let bDateTime = Date.parse(b.date);
            return bDateTime - aDateTime;
        });
    }).catch((error) => {
        console.log(error);
    });

    BreadcrumbStore.set({ path: [
        { url: "/registers", name: "Registers" },
        { url: "/registers/documentcontrol", name: "Document Control" },
    ] });
</script>

<div class="sm:flex sm:items-center mb-4 mt-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Document Control Register
        </div>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/registers/documentcontrol/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Add Document Control</button
        >
    </div>
</div>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2 mt-5">
    Approved documents
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    <!-- display documents with approver, updated revision date -->
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2 mt-5">
    Expired documents
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    <!-- display documents passed their revision date here -->
</ul>