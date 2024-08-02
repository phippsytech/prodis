<script>
    import Papa from "@app/../node_modules/papaparse";

    export let json_data = [];
    export let filename = "data.csv";
    export let options = [];
    export let label = "Export CSV";

    function convertJSONtoCSV() {
        try {
            const csv = Papa.unparse(json_data, options);
            downloadCSV(csv);
        } catch (err) {
            console.error("Error converting JSON to CSV:", err);
        }
    }

    // Function to trigger CSV download in the browser
    function downloadCSV(csvData) {
        const blob = new Blob([csvData], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
</script>

<button
    on:click={convertJSONtoCSV}
    type="button"
    aria-label={label}
    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
>
    <svg
        class="w-5 h-5 inline-block -mt-0 mr-1"
        data-slot="icon"
        fill="none"
        stroke-width="1.5"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
        ></path>
    </svg>
    {label}</button
>
