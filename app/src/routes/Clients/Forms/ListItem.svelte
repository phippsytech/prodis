<script>

    import {push} from 'svelte-spa-router'
    import { slide } from 'svelte/transition';
    import {ChevronRightIcon}  from 'heroicons-svelte/24/outline'
    
    export let label
    export let path;
    export let last = false;

</script>


<li 
    in:slide="{{ duration: 200 }}"
    out:slide|local="{{ duration: 200 }}"
    on:click={()=>push(path)}   
    class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {(last)?'rounded-b-lg':''}border-b border-gray-200 w-full "
>
    
    <div class="flex justify-between ">
        <h5 class="text-1xl leading-tight font-bold mb-2">{label}</h5>
        <ChevronRightIcon class="w-5 h-5"/>
        </div>
        <p class="text-xs">
            <slot></slot>
        </p>
</li>
