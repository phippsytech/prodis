<script>
    import { onMount } from "svelte";

    export let params = {};

    // Helper function to update the query string in the URL
    function updateQueryString() {
        const url = new URL(window.location);
        const searchParams = new URLSearchParams();

        console.log("params " + JSON.stringify(params));

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

    function restoreParamsFromURL() {
        const searchParams = new URLSearchParams(
            window.location.hash.split("?")[1],
        );

        const newParams = {};

        searchParams.forEach((value, key) => {
            newParams[key] = value; // Restore state from URL
        });
        params = newParams; // Trigger reactivity by reassigning params
    }

    // On page load, restore the params from the URL
    onMount(() => {
        restoreParamsFromURL();

        // Listen for popstate event to handle back/forward navigation
        window.addEventListener("popstate", restoreParamsFromURL);

        // Cleanup event listener on component unmount
        return () => {
            window.removeEventListener("popstate", restoreParamsFromURL);
        };
    });

    // Reactive: update query string when params change
    $: {
        params = { ...params };
        updateQueryString();
    }
</script>
