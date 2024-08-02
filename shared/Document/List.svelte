<script>      
      
      import {DocumentTextIcon} from 'heroicons-svelte/24/outline'
      import {XMarkIcon} from 'heroicons-svelte/24/outline'
      import { slide } from 'svelte/transition';
      import {jspa} from "../jspa.js"
      import { createEventDispatcher } from 'svelte';

      const dispatch = createEventDispatcher();

    export let documents=[];
    

    function openDocument(file_id){
        document.location = "https://drive.google.com/open?id="+file_id;
    }

    function deleteDocument(document_id){
        dispatch('delete', {document_id:document_id});
    }
    


</script>


  <div class="flex justify-center mb-3">
  <ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#if (!documents || documents.length ==0) }
      <li
        class="
          px-3
          py-2
          border-b border-gray-200
          w-full
          rounded-t-lg
          text-gray-400
          cursor-default
        "
      >
        No documents have been uploaded
      </li>
    {:else}
      {#each documents as document, index (document.id)}
        <li in:slide="{{ duration: 200 }}"
        out:slide|local="{{ duration: 200 }}" class="px-3 py-2 border-b border-gray-200 w-full rounded-t-lg  ">{#if document.status=='prepareDelete'}
          Delete <span class="font-bold">{document.name}</span>?

          <div class="flex justify-between">
            <button on:click={()=>{document.status=null}}  type="button" class="inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out">Cancel</button>
   
          <button on:click={()=>{deleteDocument(document.id)}} type="button" class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out">Delete</button>
        </div>

        {:else if document.status=="deleting"}
            Deleting file.  <span class="text-xs">This can take a moment...</span>

          {:else}
          <div class="flex justify-between">
          <div on:click={()=>openDocument(document.google_drive_file_ref)} class="cursor-pointer hover:underline hover:text-blue-600 " ><DocumentTextIcon class="w-6 h-6 inline"/> {document.name} </div>
          <div on:click={()=>{document.status='prepareDelete'}} ><XMarkIcon class="w-5 h-5  cursor-pointer" /></div>
        </div>
          {/if}
          
          </li>
      {/each}
    {/if}
  </ul>


</div>


