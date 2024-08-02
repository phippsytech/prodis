<script>
import {push,replace} from '@app/../node_modules/svelte-spa-router'


export let tabs = [
    {name: "My Account", url: "/account"},
    {name: "Company", url: "/company"},
    {name: "Team Members", url: "/team"},
    {name: "Billing", url: "/billing"},
]

export let selected="My Account";
    
function handleChange(event){
    replace(event.target.value);
}

</script>

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
    <div class="lg:hidden">
      <label for="tabs" class="sr-only">Select a tab</label>
      <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
      <select on:change={()=>handleChange(event)} id="tabs" name="tabs" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm bg-white">
        {#each tabs as tab}
        <option value={tab.url} selected={ tab.name == selected} >{tab.name}</option>
        {/each}
      </select>
    </div>
    <div class="hidden lg:block lg:px-5">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
          <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
          {#each tabs as tab}
          <a href="/#{tab.url}" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium {(tab.name == selected)?'border-indigo-500 text-indigo-600':'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'} ">{tab.name}</a>
          {/each}
          
        </nav>
      </div>
    </div>
  </div>
  