<script>
  import {push,replace} from 'svelte-spa-router'

  export let path = [];

  
</script>
  <nav class="fixed border-b border-gray-200 bg-white p-2 fixed z-20 w-full" aria-label="Breadcrumb">
    <div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8">
    <ol role="list" class="flex items-center space-x-2  ">
        <li>
          <div class="ml-2">
            <a href="/#/" class=" text-gray-400 hover:text-gray-500">
              <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
              </svg>
              <span class="sr-only">Home</span>
            </a>
          </div>
        </li>
        {#if path.length > 0}
          {#each path as item}
            <li>
              <div class="flex items-center">
                <svg class="h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
                <a href={item.url} class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700">{item.name}</a>
              </div>
            </li>
          {/each}
        {/if}

        <!-- <li>
          <div class="flex items-center">
            <svg class="h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
            </svg>
            <a href="/#/" class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700" aria-current="page">Wake Up</a>
          </div>
        </li> -->
      </ol>
    </div>
  </nav>
  <div class="h-8"></div>