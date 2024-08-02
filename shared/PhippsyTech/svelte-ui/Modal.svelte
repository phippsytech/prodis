<script>
            
    import { createEventDispatcher} from 'svelte';    

    const dispatch = createEventDispatcher();

    let visible = false;

    export function show(shift_id) {
        visible=true;
    }

    export function hide(){
        visible = false;
    }

</script>

{#if visible}
<div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-xl md:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="">
                        <div class="mt-3 text-left ">
                            
                            <h3 class="text-base  leading-6 text-gray-900" id="modal-title">
                                <slot name="title">
                                    Title
                                </slot>    
                            </h3>

                            <div class="mt-2">
                                <slot name="content">
                                    Content
                                </slot>
                            </div>

                        </div>
                    </div>
                </div>
                     
                <div class="bg-gray-50 px-4 py-3 sm:px-6">
                    <slot name="action">
                        Action
                    </slot>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}