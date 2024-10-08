<script>
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { formatDate } from "@shared/utilities.js";

    let approvedDocuments = [];
    let expiredDocuments = [];

    jspa("/Register/DocumentControl", "listDocumentControls", {}).then((result) => {
        let documentcontrols = result.result || [];

        // Get the current date for comparison
        const currentDate = new Date();

        // Filter and sort approved documents
        approvedDocuments = documentcontrols.filter(doc => {
            const revisionDate = new Date(doc.revision_date);
            const nextReviewDate = new Date(doc.next_review_date);

            // Approved if next review date is valid and greater than or equal to the current date
            return revisionDate && nextReviewDate && nextReviewDate >= currentDate;
        }).sort((a, b) => {
            // Sort approved documents based on revision date, reverse chronologically
            let aDateTime = Date.parse(a.revision_date);
            let bDateTime = Date.parse(b.revision_date);
            return bDateTime - aDateTime;
        });

        // Filter and sort expired documents
        expiredDocuments = documentcontrols.filter(doc => {
            const nextReviewDate = new Date(doc.next_review_date);

            // Expired if next review date is missing or passed the current date
            return !nextReviewDate || nextReviewDate < currentDate;
        }).sort((a, b) => {
            // Sort expired documents based on revision date, reverse chronologically
            let aDateTime = Date.parse(a.revision_date);
            let bDateTime = Date.parse(b.revision_date);
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
    Approved and updated documents
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each approvedDocuments as documentcontrol}
        <li class="p-4 border-b border-gray-200">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1">
                    <strong>#</strong> {documentcontrol.id}<br>
                    <strong>Document Number:</strong> {documentcontrol.document_number}
                </div>
                <div class="col-span-1">
                    <strong>Title:</strong> {documentcontrol.document_title}<br>
                    <strong>Staff Name:</strong> {documentcontrol.staff_name}<br>
                    <strong>Revision Date:</strong> {formatDate(documentcontrol.revision_date)}
                </div>
                <div class="col-span-1">
                    <strong>Approver:</strong> {documentcontrol.approver}<br>
                    <strong>Next Review Date:</strong> {formatDate(documentcontrol.next_review_date)}
                </div>
            </div>
        </li>
    {/each}
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2 mt-5">
    Expired documents
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each expiredDocuments as documentcontrol}
        <li class="p-4 border-b border-gray-200">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1">
                    <strong>#</strong> {documentcontrol.id}<br>
                    <strong>Document Number:</strong> {documentcontrol.document_number}
                </div>
                <div class="col-span-1">
                    <strong>Title:</strong> {documentcontrol.document_title}<br>
                    <strong>Staff Name:</strong> {documentcontrol.staff_name}<br>
                    <strong>Revision Date:</strong> {formatDate(documentcontrol.revision_date)}
                </div>
                <div class="col-span-1">
                    <strong>Approver:</strong> {documentcontrol.approver}<br>
                    <strong>Next Review Date:</strong> {formatDate(documentcontrol.next_review_date)}
                </div>
            </div>
        </li>
    {/each}
</ul>
