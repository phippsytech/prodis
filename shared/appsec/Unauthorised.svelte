<script>
    import {pop} from '@app/../node_modules/svelte-spa-router'
</script>

<div class="flex items-center justify-center p-4 " style="height:90vh">
    <div class="w-full" style="max-width:400px;">
        <div class="text-red-800 text-3xl">Unauthorised</div>
        <p class="mb-4">Your user account is not authorised to access this resource.</p>
        <button on:click={()=>pop()} type="button"
            class="inline-block px-6 py-2.5 border-2 bg-black text-white font-bold text-xs leading-tight uppercase rounded hover:text-white hover:bg-black hover:bg-opacity-50 focus:outline-none focus:ring-0 transition duration-150 ease-in-out drop-shadow-md"
            data-mdb-ripple="true" data-mdb-ripple-color="light">
            Go Back
        </button>
    </div>
</div>