<script>
    import { onMount } from "svelte";
    import { get } from "svelte/store";
    import {
        XMarkIcon,
        QuestionMarkCircleIcon,
        ExclamationTriangleIcon,
        CheckIcon,
    } from "heroicons-svelte/24/outline";

    export let jsonData = [];
    let documentsData = []; // JSON data
    let staffNames = [];
    let documentNames = [];

    // Parse JSON data and extract unique staff names and document names
    $: {
        // Parse JSON data
        documentsData = jsonData;

        // Extract unique staff names
        staffNames = [...new Set(documentsData.map((item) => item.staff_name))];

        // Extract unique document names
        documentNames = [
            ...new Set(documentsData.map((item) => item.document_name)),
        ];
    }

    // Function to determine status indicator for a document
    function getStatus(staffName, documentName) {
        const document = documentsData.find(
            (item) =>
                item.staff_name === staffName &&
                item.document_name === documentName,
        );
        if (document.document_status === "Missing") return "?"; // document missing
        if (!document.expiry_date) return "✓"; // No expiry date means current document

        // Parse expiry date as Date object
        const expiryDate = new Date(document.expiry_date);
        const currentDate = new Date();

        // Calculate days until expiry
        const daysUntilExpiry = Math.floor(
            (expiryDate - currentDate) / (1000 * 60 * 60 * 24),
        );

        if (daysUntilExpiry < 0) return "X"; // Expired document
        if (daysUntilExpiry <= 42) return "!"; // Expiring within next 6 weeks
        return "✓"; // Current document
    }
</script>

<table class="min-w-full">
    <thead>
        <tr class=" divide-x divide-slate-200">
            <th></th>
            {#each documentNames as documentName}
                <th class="font-normal vertical text-left p-2"
                    >{documentName}</th
                >
            {/each}
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-200">
        {#each staffNames as staffName, index}
            <tr
                class="divide-x divide-y divide-slate-200 {index % 2 === 0
                    ? 'bg-slate-100'
                    : 'bg-white'} "
            >
                <td class="font-bold text-right p-2">{staffName}</td>
                {#each documentNames as documentName}
                    <td class="text-center p-2">
                        {#if getStatus(staffName, documentName) === "?"}
                            <QuestionMarkCircleIcon
                                class="text-slate-400 w-5 h-5 inline-block"
                            />
                        {/if}
                        {#if getStatus(staffName, documentName) === "!"}
                            <ExclamationTriangleIcon
                                class="text-yellow-600 w-5 h-5 inline-block"
                            />
                        {/if}
                        {#if getStatus(staffName, documentName) === "✓"}
                            <CheckIcon
                                class="text-green-600 w-5 h-5 inline-block"
                            />
                        {/if}
                        {#if getStatus(staffName, documentName) === "X"}
                            <XMarkIcon
                                class="text-red-600 w-5 h-5 inline-block"
                            />
                        {/if}
                    </td>
                {/each}
            </tr>
        {/each}
    </tbody>
</table>

<div
    class="flex justify-between px-3 py-2 border my-4 text-sm text-gray-600 rounded-md"
>
    <div>
        <QuestionMarkCircleIcon class="text-slate-400 w-5 h-5 inline-block" />=
        Missing
    </div>

    <div>
        <ExclamationTriangleIcon
            class="text-yellow-600 w-5 h-5 inline-block"
        />= Expiring
    </div>

    <div>
        <CheckIcon class="text-green-600 w-5 h-5 inline-block" />= Current
    </div>

    <div><XMarkIcon class="text-red-600 w-5 h-5 inline-block" />= Expired</div>
</div>

<style>
    .vertical {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
    }

    @media print {
        tr {
            -webkit-print-color-adjust: exact; /* For WebKit browsers */
            background-color: inherit !important; /* Use the row's background color */
        }
        .no-print {
            display: none !important;
        }
    }
</style>
