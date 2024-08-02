<script>

    import {ChevronRightIcon} from '@app/../node_modules/heroicons-svelte/dist/24/outline'
    
    export let label
    

</script>


<div on:click  class="p-4 rounded-lg shadow-lg bg-white w-full hover:bg-blue-700 hover:shadow-lg hover:text-white  duration-150 ease-in-out mb-2">
    <div class="flex justify-between ">

        
    <h5 class="text-1xl leading-tight font-bold mb-2">{label}</h5>
    <ChevronRightIcon class="w-5 h-5"/>
    </div>
    <p class="text-xs">
        <slot></slot>
    </p>
    </div>