<script>
    
    import {push} from 'svelte-spa-router';

    function logout(){
        localStorage.removeItem('keys');
        localStorage.removeItem('JWT');
        localStorage.removeItem('isAuthenticated');
        push('/');
    }


</script>

<div class="flex items-center justify-center p-4 " style="height:100vh">
    <div class="w-full" style="max-width:400px;">
        <p class="text-gray-700  uppercase mb-0">Crystel Care</p>
        <div class="text-3xl mb-4">Unauthorised</div>
<hr class="mb-4"/>
        <p class="mb-2 px-4">Login worked but you may not proceed.</p>
        <p class="mb-2 px-4 text-xs opacity-50">Talk to admin if you think you should have access.</p>
        <a on:click={()=>logout()} class="mb-2 px-4 text-xs ">Logout.</a>
        
    </div>
</div>


