<script>
  import Container from "@shared/Container.svelte";

  let verifyList = [
    {
      id: 1,
      name: "John Doe",
      document: "CPR",
    },
    {
      id: 2,
      name: "Jane Smith",
      document: "First Aid",
    },
    {
      id: 3,
      name: "Bob Johnson",
      document: "CPR",
    },
    {
      id: 4,
      name: "Alice Lee",
      document: "First Aid",
    },
    {
      id: 5,
      name: "Tom Wilson",
      document: "CPR",
    },
    {
      id: 6,
      name: "Samantha Brown",
      document: "First Aid",
    },
    {
      id: 7,
      name: "David Kim",
      document: "CPR",
    },
    {
      id: 8,
      name: "Emily Chen",
      document: "First Aid",
    },
    {
      id: 9,
      name: "Michael Davis",
      document: "CPR",
    },
    {
      id: 10,
      name: "Olivia Rodriguez",
      document: "First Aid",
    },
  ];
</script>

<div class="mb-2">
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Verification Required
  </div>
  <p class=" text-sm text-gray-700">
    Staff supplied documents that need to be verified.
  </p>
</div>

<table class="min-w-full divide-y divide-gray-300">
  <thead class=" text-xs">
    <tr>
      <th class="p-2 text-left font-medium">Staff Name</th>
      <th class="p-2 text-left font-medium">Document</th>
      <th class="p-2 text-right font-medium">Action</th>
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-200 bg-white">
    {#each verifyList as verify, index (verify.id)}
      <tr
        in:slide={{ duration: 200 }}
        out:slide={{ duration: 200 }}
        on:click={() => push("/verifys/" + verify.id)}
        class="px-6 py-2 hover:bg-indigo-600 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 cursor-pointer {verifyList.length -
          1 ==
        index
          ? 'rounded-b-lg'
          : ''}border-b border-gray-200 w-full {verify.archived == 1
          ? 'text-gray-400 '
          : 'text-gray-900 '}"
      >
        <td class=" flex items-center pr-2 pl-2">{verify.name}</td>
        <td class="whitespace-nowrap py-2 text-gray-900 text-left pl-0 text-xs"
          >{verify.document}</td
        >
        <td class="whitespace-nowrap px-3 py-2 text-sm text-right">action</td>
      </tr>
    {:else}
      <tr>
        <td colspan="3">
          <div
            class="relative flex justify-center items-center space-x-3 rounded-lg bg-white px-10 py-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:bg-indigo-600 hover:text-white group"
          >
            <svg
              class="animate-spin flex-shrink-0 h-5 w-5 text-indigo-600"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
          </div>
        </td>
      </tr>
    {/each}
  </tbody>
</table>
