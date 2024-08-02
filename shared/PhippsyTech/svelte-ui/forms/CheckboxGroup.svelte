<script>
    
    import { onMount, afterUpdate } from 'svelte';
    import {makeUniqueId} from '../js/base.js'

    export let values= []
    export let options= []
     
    const id=makeUniqueId();

    options.forEach(option => {
        option.checked = values.some(item => item == option.value)
    });

    function getCheckedValues() {
        values = options.filter(option => option.checked).map(option => option.value);
    }

   
</script>


   <div class=" space-y-1">
    {#each options as option, index }
    <div class="relative flex gap-x-3">
      <div class="flex h-6 items-center">
        <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"  bind:checked={option.checked} id={id+index} on:change={() => getCheckedValues()}>
      </div>
        <label class="font-medium text-gray-900" for={id+index} >{option.option}</label>
    </div>
    {:else}
               ERR: No options provided
           {/each}

</div>

