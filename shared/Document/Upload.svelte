<script>
    import FloatingInput from '../PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import Button from '../PhippsyTech/svelte-ui/Button.svelte';;
    import {PlusIcon} from 'heroicons-svelte/24/outline'
    import Roadblock from '../appsec/Roadblock.svelte';
    import {checkGoogleDriveRoadblock} from '../roadblocks/GoogleDrive.svelte'
    
    let roadblockChecks=[checkGoogleDriveRoadblock];

    let document = {}
</script>

<Roadblock roadblockChecks={roadblockChecks} >

<h1 class="text-black md:text-5xl text-3xl font-bold mt-0 mb-2 drop-shadow mb-2">Upload Document</h1>
                  
                
<FloatingInput bind:value={document.name} label="Document Name" placeholder="eg: Cert 4 in Disability support" />
<FloatingInput bind:value={document.file} label="File" placeholder="pick file to upload" />

  <div class="flex justify-between ">
      <span></span>
      <Button label="Upload"  />
  </div>

  </Roadblock>