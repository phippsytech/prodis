<script>
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa";
    import JSON2CSV from "@shared/JSON2CSV.svelte";
    import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";
    import { getDaysUntilDate, formatDate } from "@shared/utilities.js";
    import { debounce } from "lodash-es";
    import CredentialGrid from "./CredentialGrid.svelte";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let staff = [];
    let staffList = [];
    let selectedStaff = [];

    BreadcrumbStore.set({
        path: [{ url: null, name: "Credentials" }],
    });

    jspa("/Staff", "listStaff", {}).then((result) => {
        staff = result.result;

        staff.forEach((staffer) => {
            if (staffer.archived != 1)
                staffList.push({
                    option: staffer.first_name + " " + staffer.last_name,
                    value: staffer.id,
                });
        });

        staffList.sort((a, b) => a.option.localeCompare(b.option));

        staffList = staffList;
    });

    let credentials = [];
    let requestCounter = 0;

    function loadCredentials(selectedStaff) {
        const currentRequest = ++requestCounter; // Increment counter on each call
        if (selectedStaff.length === 0) {
            credentials = []; // Immediately clear credentials if no staff selected
            return;
        }

        jspa("/Credential", "listCredentialsByStaffIds", {
            staff_ids: selectedStaff,
        }).then((result) => {
            if (currentRequest === requestCounter) {
                // Check if this is the latest request
                //credentials = result.result;

                // this will sort the credentials by expiry date
                credentials = result.result.sort((a, b) => {
                    if (!a.expiry_date) return 1; // Place credentials without expiry date at the end
                    if (!b.expiry_date) return -1; // Place credentials without expiry date at the end
                    const dateA = new Date(a.expiry_date);
                    const dateB = new Date(b.expiry_date);
                    return dateA - dateB;
                });
            }
        });
    }

    function hasDatePassed(dateString) {
        const today = new Date();
        const expiryDate = new Date(dateString);
        return expiryDate < today;
    }

    function isDateWithinSixWeeks(dateString) {
        const SIX_WEEKS_IN_MS = 6 * 7 * 24 * 60 * 60 * 1000;
        const today = new Date();
        const sixWeeksLater = new Date(today.getTime() + SIX_WEEKS_IN_MS); // 6 weeks in milliseconds
        const targetDate = new Date(dateString);

        return targetDate < sixWeeksLater && targetDate >= today;
    }

    // Define the debounced function
    const debouncedLoadCredentials = debounce(loadCredentials, 50);

    // Reactive statement to call the debounced function
    $: selectedStaff, debouncedLoadCredentials(selectedStaff);

    const currentDate = new Date();
    const options = {
        day: "numeric",
        month: "long",
        year: "numeric",
        timeZone: "Australia/Sydney",
    };
    const formattedDate = currentDate.toLocaleDateString("en-AU", options);
    // console.log(formattedDate);

    // TODO: This is a duplicate of code found in Credential.svelte.  This should be refactored into a shared utility function.
    function openFile(vultr_storage_ref) {
        SpinnerStore.set({ show: true, message: "Getting File" });

        jspa("/Storage", "getS3ObjectFile", { key: vultr_storage_ref })
            .then((result) => {
                //console.log(result)
                let fileContent = result.result;
                let fileName = vultr_storage_ref.substring(33);

                // Decode base64 content
                let decodedContent = atob(fileContent);
                let byteNumbers = new Array(decodedContent.length);
                for (let i = 0; i < decodedContent.length; i++) {
                    byteNumbers[i] = decodedContent.charCodeAt(i);
                }
                let byteArray = new Uint8Array(byteNumbers);

                // Determine the MIME type based on the file extension or content
                let mimeType;
                if (fileName.endsWith(".pdf")) {
                    mimeType = "application/pdf";
                } else if (fileName.endsWith(".txt")) {
                    mimeType = "text/plain";
                } else if (
                    fileName.endsWith(".jpg") ||
                    fileName.endsWith(".jpeg")
                ) {
                    mimeType = "image/jpeg";
                } else if (fileName.endsWith(".png")) {
                    mimeType = "image/png";
                } else {
                    mimeType = "application/octet-stream";
                }

                const blob = new Blob([byteArray], { type: mimeType });

                const url = URL.createObjectURL(blob);

                // Open the URL in a new tab
                window.open(url, "_blank");

                // Optionally revoke the URL after a short delay
                setTimeout(() => {
                    URL.revokeObjectURL(url);
                }, 10000); // adjust the timeout as needed
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }
</script>

<div class="no-print">
    <div class="mb-8">
        <div class="mb-2">
            <div
                class="text-2xl sm:truncate tracking-tight font-fredoka-one-regular text-indigo-950"
            >
                Required Credentials Report
            </div>
            <p class=" text-sm text-gray-700">
                Select the staff you want to view required credentials for.
            </p>
        </div>

        <CheckboxButtonGroup options={staffList} bind:values={selectedStaff} />
    </div>
</div>
{#if credentials.length > 0}
    <div class="mb-2">
        <div class="flex justify-between items-center">
            <div>
                <div
                    class="text-2xl sm:truncate tracking-tight font-fredoka-one-regular text-indigo-950"
                >
                    Staff Credentials
                </div>
                <p class=" text-sm text-gray-700">
                    Here are the required credentials for the selected staff as
                    of {formattedDate}.
                </p>
            </div>

            <div class="no-print">
                <JSON2CSV
                    filename="credentials.csv"
                    bind:json_data={credentials}
                    label="Export results to CSV"
                />
            </div>
        </div>
    </div>

    <CredentialGrid bind:jsonData={credentials} />

    <!-- <div class="no-print"> -->
    <table class="min-w-full divide-y divide-gray-300">
        <thead class=" text-xs">
            <tr>
                <th class="p-2 text-left font-medium">Name</th>
                <th class="p-2 text-left font-medium">Credential</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            {#each credentials as credential, index (index)}
                <tr
                    in:slide={{ duration: 200 }}
                    out:slide={{ duration: 200 }}
                    class={credential.credential_status === "Missing"
                        ? "text-gray-400"
                        : ""}
                >
                    <td class="p-2 text-left font-medium"
                        >{credential.staff_name}</td
                    >
                    <td class="p-2 text-left font-medium">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold">
                                    {#if credential.file_reference}
                                        <button
                                            on:click={() => {
                                                openFile(
                                                    credential.file_reference,
                                                );
                                            }}
                                        >
                                            {credential.credential_name}
                                            <svg
                                                class="h-4 w-4 inline"
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
                                                    d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"
                                                ></path>
                                            </svg>
                                        </button>
                                    {:else}
                                        {credential.credential_name}
                                    {/if}
                                </div>
                                {#if credential.credential_details}
                                    <div class="text-xs">
                                        {credential.credential_details}
                                    </div>
                                {/if}

                                {#if credential.expiry_date && !isDateWithinSixWeeks(credential.expiry_date) && !hasDatePassed(credential.expiry_date)}
                                    <div class="text-xs">
                                        Expires {formatDate(
                                            credential.expiry_date,
                                        )}
                                    </div>
                                {/if}

                                {#if credential.issue_date && !credential.expiry_date}
                                    <div class="text-xs">
                                        Issued {formatDate(
                                            credential.issue_date,
                                        )}
                                    </div>
                                {/if}
                                {#if credential.expiry_date && isDateWithinSixWeeks(credential.expiry_date)}
                                    <div class="text-xs">
                                        Renew by {formatDate(
                                            credential.expiry_date,
                                        )} ({getDaysUntilDate(
                                            credential.expiry_date,
                                        )})
                                    </div>
                                {/if}

                                {#if credential.expiry_date && hasDatePassed(credential.expiry_date)}
                                    <div class="text-xs">
                                        Expired {formatDate(
                                            credential.expiry_date,
                                        )}
                                        ({getDaysUntilDate(
                                            credential.expiry_date,
                                        )})
                                    </div>
                                {/if}
                            </div>

                            {#if credential.expiry_date && hasDatePassed(credential.expiry_date)}
                                <span
                                    class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700"
                                    >Expired</span
                                >
                            {:else if credential.credential_status === "Missing"}
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600"
                                    >Missing</span
                                >
                            {:else if credential.expiry_date && isDateWithinSixWeeks(credential.expiry_date)}
                                <span
                                    class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800"
                                    >Renewal Due</span
                                >
                            {:else}
                                <span
                                    class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700"
                                    >Current</span
                                >
                            {/if}
                        </div>
                    </td>
                </tr>
            {/each}
        </tbody>
    </table>
    <!-- </div> -->
{/if}

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
