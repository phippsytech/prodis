<script>
    export let params = {};

    // Helper function to update the query string in the URL
    function updateQueryString() {
        console.log("updateQueryString " + JSON.stringify(params));

        const url = new URL(window.location);
        const searchParams = new URLSearchParams();

        // Loop through the params object and update the query string
        Object.entries(params).forEach(([key, value]) => {
            if (value) {
                searchParams.set(key, value); // Set if value exists
            } else {
                searchParams.delete(key); // Remove if value is null/empty
            }
        });

        // Construct the new URL with the hash fragment and query string
        const newUrl = `${url.origin}${url.pathname}${url.hash.split("?")[0]}?${searchParams.toString()}`;
        window.history.pushState({}, "", newUrl); // Update URL without reload
    }

    // Reactive: update query string when params change
    $: updateQueryString(params);
</script>
