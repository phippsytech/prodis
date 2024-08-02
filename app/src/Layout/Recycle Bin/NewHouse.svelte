<script>
    import HouseForms from '../routes/House/Forms/List.svelte';
    import Breadcrumbs from './Breadcrumbs.svelte';
    import { onMount } from 'svelte';
    
    import { HouseStore, StafferStore } from '@shared/stores.js';

    // export let params = {}

    let params = {
        form: "WakeUp"
    }
    let component;

    $: stafferStore = $StafferStore;
    $: houseStore = $HouseStore;

    onMount(async () => {
        component = (await import('../routes/House/Forms/Form/'+params.form+'.svelte')).default;
    });
</script>

  <div class="xl:hidden">
      <Breadcrumbs />
    </div>

      <div class="xl:pl-96">
        <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-6">
          <!-- Main area -->
          <svelte:component this={component} staff_id={stafferStore.id} participant_id={houseStore.client_id} />
        </div>
      </div>

      <aside class="fixed inset-y-0 left-72 hidden w-96 overflow-y-auto border-r border-gray-200  bg-white  xl:block">
        <HouseForms />
      <!-- Secondary column (hidden on smaller screens) -->
    </aside>


  