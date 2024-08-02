<script>
/*
There is an issue with my current solution.
Any reactive data is no longer reactive because the html is already rendered when it gets inserted in the tab.


This is a known issue.  This particular svelte issue in github has some workarounds.

https://github.com/sveltejs/svelte/issues/6493

*/



 import {onMount} from 'svelte';

    export let tabs=[];
    export let active = null;

    let container;

    let active_tab;

onMount(()=>{
    
    active_tab = container.querySelector(`#`+active).innerHTML;
    
});
	function setActive(tab){

        active = tab;
        
        active_tab = container.querySelector(`#`+tab).innerHTML;

	}

</script>


<nav class="navbar navbar-expand-lg mb-4">
  
    <ul class="navbar-nav flex flex-col pl-0 list-style-none mr-auto">
        {#each tabs as tab}
            <li class="nav-item p-2 {(active==tab)?'bg-black text-white':' text-gray-500 hover:text-gray-700 focus:text-gray-700'} ">
                <a on:click={()=>setActive(tab)} class=" block nav-link  p-0" >{tab}</a>
            </li>
        {/each}
    </ul>

</nav>

<span style="display:none" bind:this={container}><slot/></span>

{@html active_tab}