<script>
    
    import HouseNavList from './HouseNavList.svelte';
    import TherapyNavList from './TherapyNavList.svelte';
    import MenuItem from '../../MenuItem.svelte'

    export let client={}

    const pathname = window.location.hash;
    const segments = pathname.split('/');
    let  selected = segments[3];






</script>


{#if client.id}

<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col  -mx-2 ">

        <li>
            <MenuItem label="Details" href="/#/clients/{client.id}" selected={selected=="details"} >
                <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 576 512"><path d="M512 80c8.8 0 16 7.2 16 16V416c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V96c0-8.8 7.2-16 16-16H512zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zM208 256a64 64 0 1 0 0-128 64 64 0 1 0 0 128zm-32 32c-44.2 0-80 35.8-80 80c0 8.8 7.2 16 16 16H304c8.8 0 16-7.2 16-16c0-44.2-35.8-80-80-80H176zM376 144c-13.3 0-24 10.7-24 24s10.7 24 24 24h80c13.3 0 24-10.7 24-24s-10.7-24-24-24H376zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24h80c13.3 0 24-10.7 24-24s-10.7-24-24-24H376z"/></svg>
          </MenuItem>
          </li>
       
          {#if client.sil_enabled }
        <HouseNavList bind:client={client}  bind:selected={selected} />
        {/if}
    
        <TherapyNavList bind:client={client}  bind:selected={selected} />

    </ul>
  </nav>

  {/if}