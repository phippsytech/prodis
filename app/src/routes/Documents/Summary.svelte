<script>
  import Container from "@shared/Container.svelte";
</script>

<div class="mb-2">
  <div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
    style="color:#220055;"
  >
    Document Summary
  </div>
  <p class=" text-sm text-gray-700">
    This is a summary of outstanding required documents for staff members.
  </p>
</div>

<!--
  This example requires some changes to your config:
  
  ```
  // tailwind.config.js
  module.exports = {
    // ...
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
  ```
-->
<div>
  <div class="sm:hidden">
    <label for="tabs" class="sr-only">Select a tab</label>
    <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
    <select
      id="tabs"
      name="tabs"
      class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
    >
      <option>Verification Required</option>
      <option>Missing Documents</option>
      <option selected>Expired Documents</option>
      <option>Due for Renewal</option>
    </select>
  </div>
  <div class="hidden sm:block">
    <div class="border-b border-gray-200">
      <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:border-gray-200 hover:text-gray-700" -->
        <a
          href="/#/"
          class="border-transparent text-gray-500 hover:border-gray-200 hover:text-gray-700 flex whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium"
        >
          Verification Required
          <!-- Current: "bg-indigo-100 text-indigo-600", Default: "bg-gray-100 text-gray-900" -->
          <span
            class="bg-gray-100 text-gray-900 ml-3 hidden rounded-full py-0.5 px-2.5 text-xs font-medium md:inline-block"
            >52</span
          >
        </a>
        <a
          href="/#/"
          class="border-transparent text-gray-500 hover:border-gray-200 hover:text-gray-700 flex whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium"
        >
          Missing Documents
          <span
            class="bg-gray-100 text-gray-900 ml-3 hidden rounded-full py-0.5 px-2.5 text-xs font-medium md:inline-block"
            >6</span
          >
        </a>
        <a
          href="/#/"
          class="border-indigo-500 text-indigo-600 flex whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium"
          aria-current="page"
        >
          Expired Documents
          <span
            class="bg-indigo-100 text-indigo-600 ml-3 hidden rounded-full py-0.5 px-2.5 text-xs font-medium md:inline-block"
            >4</span
          >
        </a>
        <a
          href="/#/"
          class="border-transparent text-gray-500 hover:border-gray-200 hover:text-gray-700 flex whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium"
        >
          Due For Renewal
        </a>
      </nav>
    </div>
  </div>
</div>

<Container>
  <h2 class="font-medium text-gray-900">Verification Required</h2>
  <p class="text-gray-500 text-sm">
    Staff supplied documents that need to be verified.
  </p>
  <ul
    role="list"
    class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-3 lg:grid-cols-3"
  >
    <li
      class="col-span-1 flex rounded-md shadow-sm items-top border rounded-md border-gray-200 bg-white px-4 py-3"
    >
      <div class="flex-1 text-sm">
        <a href="/#/" class="font-bold text-gray-900 hover:text-indigo-600"
          >Michael Phipps</a
        >
        <p class="text-gray-500">
          Right To Work, Driver's Licence, Vehicle Insurance, HLTAID009 CPR
        </p>
      </div>
    </li>
  </ul>
</Container>

<Container>
  <h2 class="font-medium text-gray-900">Missing Documents</h2>
  <p class="text-gray-500 text-sm">
    Staff who do not have documents that have been marked required.
  </p>
  <ul
    role="list"
    class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-3 lg:grid-cols-3"
  >
    <li
      class="col-span-1 flex rounded-md shadow-sm items-top border rounded-md border-gray-200 bg-white px-4 py-3"
    >
      <div class="flex-1 text-sm">
        <a href="/#/" class="font-bold text-gray-900 hover:text-indigo-600"
          >Michael Phipps</a
        >
        <p class="text-gray-500">
          Right To Work, Driver's Licence, Vehicle Insurance, HLTAID009 CPR
        </p>
      </div>
    </li>
  </ul>
</Container>

<Container>
  <h2 class="font-medium text-gray-900">Expired Documents</h2>
  <p class="text-gray-500 text-sm">Supplied documents that have expired.</p>
  <ul
    role="list"
    class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-3 lg:grid-cols-3"
  >
    <li
      class="col-span-1 flex rounded-md shadow-sm items-top border rounded-md border-gray-200 bg-white px-4 py-3"
    >
      <div class="flex-1 text-sm">
        <a href="/#/" class="font-bold text-gray-900 hover:text-indigo-600"
          >Michael Phipps</a
        >
        <p class="text-gray-500">
          Right To Work, Driver's Licence, Vehicle Insurance, HLTAID009 CPR
        </p>
      </div>
    </li>
  </ul>
</Container>

<Container>
  <h2 class="font-medium text-gray-900">Due for Renewal</h2>
  <p class="text-gray-500 text-sm">Documents that will expire soon</p>
  <ul
    role="list"
    class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-3 lg:grid-cols-3"
  >
    <li
      class="col-span-1 flex rounded-md shadow-sm items-top border rounded-md border-gray-200 bg-white px-4 py-3"
    >
      <div class="flex-1 text-sm">
        <a href="/#/" class="font-bold text-gray-900 hover:text-indigo-600"
          >Michael Phipps</a
        >
        <p class="text-gray-500">
          Right To Work, Driver's Licence, Vehicle Insurance, HLTAID009 CPR
        </p>
      </div>
    </li>
  </ul>
</Container>
