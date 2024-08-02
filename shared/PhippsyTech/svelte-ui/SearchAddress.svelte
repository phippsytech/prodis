<script>
    /**
     * This is a simple search input that uses the Mapbox API to search for addresses.
     * It requires the Phippsy Tech Mapbox searchAddress endpoint to be available on the server.
     */

    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";

    let searchText = "";
    let suggestions = [];

    async function searchAddress(query) {
        if (query.length < 3) return; // Avoid searching for too short strings
        jspa("/Geo", "searchAddress", { query: query }).then((result) => {
            suggestions = result.result.suggestions;
        });
    }

    function selectSuggestion(suggestion) {
        searchText = suggestion.name; // Update the search text with the selected suggestion
        suggestions = []; // Clear suggestions
    }

    // Debounce the search to reduce API calls
    let timeout;
    $: {
        clearTimeout(timeout);
        timeout = setTimeout(() => searchAddress(searchText), 250);
    }
</script>

<FloatingInput
    bind:value={searchText}
    placeholder="Search for an address..."
    label="Search Address"
/>

{#if suggestions.length}
    <ul>
        {#each suggestions as suggestion}
            <li on:click={() => selectSuggestion(suggestion)}>
                <div class="font-semibold">{suggestion.name}</div>
                <div class="text-xs">
                    {#if suggestion.context.locality}
                        {suggestion.context.locality.name}
                    {:else}
                        {suggestion.context.place.name}
                    {/if}

                    {suggestion.context.region.region_code}
                    {suggestion.context.postcode.name}
                </div>
            </li>
        {/each}
    </ul>
{/if}
