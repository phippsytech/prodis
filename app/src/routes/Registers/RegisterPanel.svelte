<script>
    import { ChevronRightIcon } from "heroicons-svelte/24/outline";

    export let label;
</script>

<div
    on:click
    class="p-4 rounded-lg shadow-lg bg-white w-full hover:bg-indigo-600 hover:shadow-lg hover:text-white duration-150 ease-in-out mb-2 cursor-pointer"
>
    <div class="flex justify-between items-center">
        <div>
            <h5 class="text-1xl leading-tight font-bold mb-2">{label}</h5>
            <p class="text-xs">
                <slot></slot>
            </p>
        </div>

        <ChevronRightIcon class="w-5 h-5" />
    </div>
</div>
