<script>
    import { ModalStore } from './stores.js';
  
    $: modal = $ModalStore;

    function closeModal(){
        ModalStore.set({ show: false });
    }

    function action(){
        modal.action()
        closeModal()
    }

    function del(){
        modal.delete()
        closeModal()
    }

  </script>
  
{#if modal.show}
<div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto" >
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-xl md:max-w-lg">
                <div class="bg-white py-3  px-4 ">
                    <div class="">
                        <div class="mt-1 text-left ">
                            
                            <h3 class="text-base  leading-6 text-gray-900" id="modal-title">
                                <slot name="title">
                                    <b>{modal.label}</b>
                                </slot>
                            </h3>
                            {#if modal.description}<div class="text-xs">{modal.description}</div>{/if}

                            <div class="mt-2">
                                <slot name="content">
                                    <svelte:component this={modal.component} bind:props={modal.props} />
                                </slot>
                            </div>

                        </div>
                    </div>
                </div>
                     
                <div class="bg-gray-50 px-4 py-3 sm:px-6">
                    <slot name="action">
                        <div class="flex justify-between">
                        {#if modal.delete}    <button on:click={()=>del()} type="button" class="inline-flex w-auto justify-start rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-white sm:mr-3 sm:w-auto">Delete</button>{:else}<span></span>{/if}
                            <div class="flex gap-1">
                                <button on:click={()=>closeModal()} type="button" class="w-full sm:w-auto sm:mr-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                                {#if modal.action}<button on:click={()=>action()} type="button" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">{modal.action_label}</button>{/if}
                              </div>
                        </div>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}