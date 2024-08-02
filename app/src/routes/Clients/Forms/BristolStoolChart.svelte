<script>

    export let rank =null;
    export let readOnly=false;


    let bristol_stool_options=[
        {
            rank:1,
            label:"Rabbit Droppings",
            image:"/bristol-stool-type-1.png",
            description:"Separate hard lumps, like nuts.  Hard to pass."
        },
        {
            rank:2,
            label:"Bunch of Grapes",
            image:"/bristol-stool-type-2.png",
            description:"Sausage-shapped but lumpy."
        },
        {
            rank:3,
            label:"Corn on cob",
            image:"/bristol-stool-type-3.png",
            description:"Like sausage but with cracks on the surface."
        },
        {
            rank:4,
            label:"Sausage",
            image:"/bristol-stool-type-4.png",
            description:"Like sausage or snake.  Smooth and soft."
        },
        {
            rank:5,
            label:"Chicken Nuggets",
            image:"/bristol-stool-type-5.png",
            description:"Soft blobs with clear-cuat edges.  Passed easily."
        },
        {
            rank:6,
            label:"Porridge",
            image:"/bristol-stool-type-6.png",
            description:"Fluffy pieces with ragged edges.  A mushy stool."
        },
        {
            rank:7,
            label:"Gravy",
            image:"/bristol-stool-type-7.png",
            description:"Watery.  No solid pieces.  Entirely liquid."
        }
    ]


    function selectRank(selected_rank){
        rank = selected_rank;
    }
</script>


{#if readOnly}
    <h1 class="text-black text-1xl mt-0 mb-2 mb-2">Stool type</h1>
{:else}
    <h1 class="text-black text-1xl mt-0 mb-2 mb-2">Select the stool type</h1>
{/if}

<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">


{#if readOnly}

    {#each bristol_stool_options as option}
    
        {#if rank==option.rank}
            <li 
                on:click={()=>selectRank(option.rank)}   
                class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-200 cursor-pointer "
            >
                <div class="flex justify-start items-center">
                    <img src="{option.image}" class="w-20 inline mr-4" />
                    <div>
                        <div class="font-bold">{option.rank} - {option.label}</div>  
                        <p class="text-sm">{option.description}</p>
                    </div>
                </div>    
            </li>
        {/if}
    {/each}

{:else}



    {#each bristol_stool_options as option}
    
        <li 
            on:click={()=>selectRank(option.rank)}   
            class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-200 cursor-pointer border-b border-gray-200 w-full 
            {(rank==1)?'bg-blue-700 text-white cursor-default ring-blue-500':'cursor-pointer focus:outline-offset-0 hover:bg-blue-500 hover:text-white ring-gray-300'}
            "
        >
            <div class="flex justify-start items-center">
                <img src="{option.image}" class="w-20 inline mr-4" />
                <div>
                    <div class="font-bold">{option.rank} - {option.label}</div>  
                    <p class="text-sm">{option.description}</p>
                </div>
            </div>    
        </li>

        {/each}
<!-- 
        <li 
            on:click={()=>selectRank(2)}   
            class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-200 cursor-pointer border-b border-gray-200 w-full 
            {(rank==2)?'bg-blue-700 text-white cursor-default ring-blue-500':'cursor-pointer focus:outline-offset-0 hover:bg-blue-500 hover:text-white ring-gray-300'}
            "
        >
        <div class="flex justify-start items-center">
            <img src="/bristol-stool-type-2.png" class="w-20 inline mr-4" />
            <div>
                <div class="font-bold">2 - Bunch of Grapes</div>
                <p class="text-sm">Sausage-shapped but lumpy.</p>
                </div>
            </div>
        </li>
    
        <li 
            on:click={()=>selectRank(3)}   
            class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-200 cursor-pointer border-b border-gray-200 w-full 
            {(rank==3)?'bg-blue-700 text-white cursor-default ring-blue-500':'cursor-pointer focus:outline-offset-0 hover:bg-blue-500 hover:text-white ring-gray-300'}
            "
        >
            <div class="flex justify-start items-center">
                <img src="/bristol-stool-type-3.png" class="w-20 inline mr-4" />
                <div>
                    <div class="font-bold">3 - Corn on cob</div>
                    <p class="text-sm">Like sausage but with cracks on the surface.</p>
                </div>
                
            </div>
        </li>
    
        <li 
            on:click={()=>selectRank(4)}   
            class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-200 cursor-pointer border-b border-gray-200 w-full 
            {(rank==4)?'bg-blue-700 text-white cursor-default ring-blue-500':'cursor-pointer focus:outline-offset-0 hover:bg-blue-500 hover:text-white ring-gray-300'}
            "
        >
        <div class="flex justify-start items-center">
            <img src="/bristol-stool-type-4.png" class="w-20 inline mr-4" />
            <div>
                <div class="font-bold">4 - Sausage</div>
                <p class="text-sm">Like sausage or snake.  Smooth and soft.</p>
            </div>
            
            </div>
        </li>
    
        <li 
            on:click={()=>selectRank(5)}   
            class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-200 cursor-pointer border-b border-gray-200 w-full 
            {(rank==5)?'bg-blue-700 text-white cursor-default ring-blue-500':'cursor-pointer focus:outline-offset-0 hover:bg-blue-500 hover:text-white ring-gray-300'}
            "
        >
        <div class="flex justify-start items-center">
            <img src="/bristol-stool-type-5.png" class="w-20 inline mr-4" />
            <div>
                <div class="font-bold">5 - Chicken Nuggets</div>
                <p class="text-sm">Soft blobs with clear-cuat edges.  Passed easily.</p>
            </div>
            
            </div>
        </li>
    
        <li 
            on:click={()=>selectRank(6)}   
            class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-200 cursor-pointer border-b border-gray-200 w-full 
            {(rank==6)?'bg-blue-700 text-white cursor-default ring-blue-500':'cursor-pointer focus:outline-offset-0 hover:bg-blue-500 hover:text-white ring-gray-300'}
            "
        >
        <div class="flex justify-start items-center">
            <img src="/bristol-stool-type-6.png" class="w-20 inline mr-4" />
            <div>
                <div class="font-bold">6 - Porridge</div>
                <p class="text-sm">Fluffy pieces with ragged edges.  A mushy stool.</p>
            </div>
            
            </div>
        </li>
    
        <li 
        on:click={()=>selectRank(7)}   
        class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-200 cursor-pointer rounded-b-lg border-b border-gray-200 w-full 
        {(rank==7)?'bg-blue-700 text-white cursor-default ring-blue-500':'cursor-pointer focus:outline-offset-0 hover:bg-blue-500 hover:text-white ring-gray-300'}
        "
    >
        
        <div class="flex justify-start items-center">
            <img src="/bristol-stool-type-7.png" class="w-20 inline mr-4" />
            <div>
                <div class="font-bold">7 - Gravy</div>
                <p class="text-sm">Watery.  No solid pieces.  Entirely liquid.</p>
            </div>
            
            </div>
        </li> -->


{/if}

</ul>