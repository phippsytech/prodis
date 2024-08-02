<script>

    export let steps=[{
        label: 'Step 1',
        url: '/steps/1',
        status: 'complete'
    },{
        label: 'Step 2',
        url: '/steps/2',
        status: 'current'
    },{
        label: 'Step 3',
        url: '/steps/3',
        status: 'upcoming'
    }];

</script>

<nav aria-label="Progress">
    <ol role="list" class="divide-y divide-gray-300 rounded-md border border-gray-300 md:flex md:divide-y-0">
      
        {#each steps as step, index}
        <li class="relative md:flex md:flex-1">
        {#if step.status=='complete'}
        
        <!-- Completed Step -->
        <a href="/#{step.url}" class="group flex w-full items-center">
          <span class="flex items-center px-6 py-4 text-sm font-medium">
            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-600 group-hover:bg-indigo-800">
              <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" />
              </svg>
            </span>
            <span class="ml-4 text-sm font-medium text-gray-900">{step.label}</span>
          </span>
        </a>
        
{/if}

{#if step.status=='current'}
      
        <!-- Current Step -->
        <a href="/#{step.url}" class="flex items-center px-6 py-4 text-sm font-medium" aria-current="step">
          <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-indigo-600">
            <span class="text-indigo-600">{index +1}</span>
          </span>
          <span class="ml-4 text-sm font-medium text-indigo-600">{step.label}</span>
        </a>
        
      {/if}

      {#if step.status=='upcoming'}
     
        <!-- Upcoming Step -->
        <span class="group flex items-center">
          <span class="flex items-center px-6 py-4 text-sm font-medium">
            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-300 ">
              <span class="text-gray-500">{index +1}</span>
            </span>
            <span class="ml-4 text-sm font-medium text-gray-500">{step.label}</span>
          </span>
        </span>
  
      {/if}

      {#if index < steps.length - 1}
      <!-- Arrow separator for lg screens and up -->
      <div class="absolute right-0 top-0 hidden h-full w-5 md:block" aria-hidden="true">
        <svg class="h-full w-full text-gray-300" viewBox="0 0 22 80" fill="none" preserveAspectRatio="none">
          <path d="M0 -2L20 40L0 82" vector-effect="non-scaling-stroke" stroke="currentcolor" stroke-linejoin="round" />
        </svg>
      </div>
      {/if}


    </li>
      {/each}
    </ol>
  </nav>
  