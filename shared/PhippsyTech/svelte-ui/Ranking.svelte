<script>
  
    // export let rating=0;
    export let readOnly=false;
    export let decimal = 10;
    export let rank =null;
    let ranking = parseInt(decimal); // this dumps the remainder if it is there

    function selectRank(selected_rank){
        rank = selected_rank;
    }

</script>

<div>
    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
            
        {#each {length: ranking} as _, i}
            {#if readOnly}
                <span class="relative inline-flex items-center px-3 py-2 text-xs font-normal text-gray-700 ring-1 ring-inset  {(i+1==rank)?'bg-blue-500 text-white ring-blue-500':'focus:outline-offset-0 ring-gray-300'} ">{i+1}</span>
            {:else}
                <span on:click={()=>selectRank(i+1)} class="relative inline-flex items-center px-3 py-2 text-xs font-normal text-gray-700 ring-1 ring-inset  {(i+1==rank)?'bg-blue-500 text-white cursor-default ring-blue-500':'cursor-pointer focus:outline-offset-0 hover:bg-blue-500/50 hover:text-white ring-gray-300'} ">{i+1}</span>
            {/if}
        {/each}





    </nav>
  </div>