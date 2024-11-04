<script>
  export let currentPage = 1; // Current page, should be a reactive value
  export let maxVisiblePages = 5; // Maximum number of pages to display
  export let resultsPerPage = 25; // Number of results per page
  export let totalResults = 0; // Total number of results

  // Ensure values are integers as soon as they are updated or converted
  $: currentPage = parseInt(currentPage) || 1;
  $: maxVisiblePages = parseInt(maxVisiblePages) || 5;
  $: resultsPerPage = parseInt(resultsPerPage) || 25;
  $: totalResults = parseInt(totalResults) || 0;

  // Calculate total pages based on totalResults and resultsPerPage.
  $: totalPages = Math.ceil(totalResults / resultsPerPage);

  // Reactive calculation of the result range.
  $: startResult = (currentPage - 1) * resultsPerPage + 1;
  $: endResult = Math.min(currentPage * resultsPerPage, totalResults);

  // Function to change the current page, called when a new page is selected.
  function goToPage(page) {
    if (page >= 1 && page <= totalPages) {
      currentPage = page;
    }
  }

  // Function to generate the pagination items
  $: paginationPages = getPagination(totalPages, currentPage, maxVisiblePages);

  function getPagination(totalPages, currentPage, maxVisiblePages) {
    const pages = [];
    pages.push(1); // Always include the first page

    // Determine start and end for the page range
    const start = Math.max(2, currentPage - Math.floor(maxVisiblePages / 2));
    const end = Math.min(
      totalPages - 1,
      currentPage + Math.floor(maxVisiblePages / 2)
    );

    // Add ellipsis before the start if necessary
    if (start > 2) {
      pages.push("...");
    }

    // Add visible page numbers between start and end
    for (let i = start; i <= end; i++) {
      pages.push(i);
    }

    // Add ellipsis after the end if necessary
    if (end < totalPages - 1) {
      pages.push("...");
    }

    // Always include the last page
    pages.push(totalPages);
    return pages;
  }
</script>

{#if totalResults > 0}
  <div class="flex items-center justify-between pt-4 px-2">
    <div class="flex flex-1 justify-between sm:hidden">
      <button
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-indigo-50"
        on:click={() => goToPage(currentPage - 1)}>Previous</button
      >
      <button
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-indigo-50"
        on:click={() => goToPage(currentPage + 1)}>Next</button
      >
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          {#if totalResults > resultsPerPage}
            <span class="font-medium">{startResult}</span>
            to
            <span class="font-medium">{endResult}</span>
            of
          {/if}
          <span class="font-medium">{totalResults}</span>
          result{#if totalResults > 1}s{/if}
        </p>
      </div>
      {#if totalResults > resultsPerPage}
        <div>
          <nav
            class="isolate inline-flex -space-x-px rounded-md shadow-sm"
            aria-label="Pagination"
          >
            <button
              class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 hover:text-indigo-600 bg-white hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
              on:click={() => goToPage(currentPage - 1)}
            >
              <span class="sr-only">Previous</span>
              <svg
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>

            {#each paginationPages as page}
              {#if page === "..."}
                <span
                  class="relative inline-flex items-center px-4 py-2 text-sm font-semibold bg-white text-gray-700 focus:outline-offset-0"
                >
                  {page}
                </span>
              {:else}
                <button
                  class="relative inline-flex items-center px-4 py-2 text-sm font-semibold {currentPage ===
                  page
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-900 hover:text-indigo-600 bg-white hover:bg-gray-50'}  focus:z-20 focus:outline-offset-0"
                  aria-current={currentPage === page ? "page" : undefined}
                  on:click={() => goToPage(page)}
                >
                  {page}
                </button>
              {/if}
            {/each}

            <button
              class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 hover:text-indigo-600 bg-white hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
              on:click={() => goToPage(currentPage + 1)}
            >
              <span class="sr-only">Next</span>
              <svg
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </nav>
        </div>
      {/if}
    </div>
  </div>
{/if}
