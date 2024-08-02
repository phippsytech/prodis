<script>

    import {pop} from 'svelte-spa-router'
    import NavBar from '@shared/NavBar.svelte'
    import Footer from '@shared/Footer.svelte'

</script>

<NavBar />

<div class="md:container mx-auto p-3  " style="height:90vh">

    <div class="grid items-center h-full ">

        <div class="text-black px-6 md:px-12 mx-auto ">
            <span class="opacity-50">404 Error</span>
            <h1 class="text-black md:text-7xl text-5xl font-bold mt-0 mb-2 drop-shadow mb-8">Not Found</h1>
            
            <button on:click={()=>pop()} type="button"
                class="inline-block px-6 py-2.5 border-2 bg-black text-white font-bold text-xs leading-tight uppercase rounded hover:text-white hover:bg-black hover:bg-opacity-50 focus:outline-none focus:ring-0 transition duration-150 ease-in-out drop-shadow-md"
                data-mdb-ripple="true" data-mdb-ripple-color="light">
                Go Back
            </button>

        </div>

      </div>

    </div>
    
<Footer />