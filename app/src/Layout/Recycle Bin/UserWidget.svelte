<script>
import { StafferStore} from '@shared/stores.js';
    
    export let direction = "down"
    let origin="right-0";

    let show=false;

    if (direction =="up"){
        origin="bottom-full left-0 ";
    }

    $: stafferStore = $StafferStore;

    function toggle(){
        show = !show;
    }

    function resetStaff(){
        StafferStore.update((currentData)=>{
            return {};
        });
    }
  

</script>

<div class="relative ml-3">
    <button on:click={()=>toggle()}  class="group  flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:text-indigo-600 hover:bg-gray-50">
        <svg class="h-8 w-8 rounded-full bg-gray-50 text-gray-300 group-hover:text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        <span class="sr-only">Your profile</span>
        {#if direction=="up"}<span aria-hidden="true">{stafferStore.name}</span>{/if}  
        </button>
    {#if show}
    <div class="absolute {origin} origin-top-right z-50 mt-2 w-56  divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
      <div class="py-1" role="none">
        <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-700" -->
        <a href="/#/profile" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-2">Your Profile</a>
      </div>
      <div class="py-1" role="none">
        <a href="/#/" on:click={()=>resetStaff()} class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-1">Switch User</a>
      </div>
    </div>
    {/if}
  </div>