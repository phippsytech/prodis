<script>


</script>


<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900 mb-2">
    <!-- {#if (!staff || staff.length ==0) }
        <li class=" px-3 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default text-sm">
            No staff have been allocated to this client
        </li>
    {:else} -->
        <!-- {#each staff as staffer, index (staffer.id)} -->
            <li class="px-3 py-2 border-b border-gray-200 w-full rounded-t-lg  ">
                <div class="flex justify-between ">
                    <div class="flex items-center ">
                        <span class="text-sm hover:underline opacity-60 pr-2">7:00am </span>
                    </div>    
                    <div class="flex items-center ">
                    <span class="font-bold  pr-2">Ibuprofen </span> 
                    <div class=" italic  pr-2 text-sm">1 x Tablet</div> 
                </div>
                
                    <div class="flex items-center ">Oral</div> 
                    
     
     
                        <!-- <div on:click={()=>removeClientStaffer(staffer.id)} class="cursor-pointer" >
                            <XMarkIcon class="w-4 h-4 inline" />
                        </div> -->
     
                </div>
            </li>
        <!-- {/each} -->
    <!-- {/if} -->
</ul>
