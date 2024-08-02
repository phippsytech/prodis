<script>
    import IconUser from "../svelte-ui/icons/User.svelte";
    import { push, replace } from "@app/../node_modules/svelte-spa-router";

    export let icon = {};

    function handleClick(icon) {
        if (icon.url) {
            replace(icon.url);
        }
        if (icon.click) {
            icon.click();
        }
    }

    /*
        The use of replace here is important, because it means the menu page doesn't clutter up browser navigation
    */
</script>

<!-- <div
    on:click={() => replace(icon.url)}
    class="transition ease-in-out text-lg p-2 sm:p-4 font-bold text-center rounded-lg bg-white hover:bg-indigo-600 hover:text-white cursor-pointer border border-indigo-100"
>
    <svelte:component this={icon.icon} class="inline h-6 w-6 sm:h-8 sm:w-8" />
    <div
        class="text-[0.625rem] sm:text-xs font-light text-tighter leading-tight mt-2"
    >
        {icon.name}
    </div>
</div> -->

<div
    on:click={() => handleClick(icon)}
    class="group transition ease-in-out text-lg p-2 font-bold text-center cursor-pointer hover:scale-125"
>
    <svelte:component
        this={icon.icon}
        class="inline h-14 w-14   rounded-lg  group-hover:bg-indigo-600 group-hover:text-white bg-white  border border-indigo-100   p-3
        
        transition duration-250 ease-in-out transform
        "
    />
    <div
        class="text-xs sm:text-xs font-light text-tighter leading-tight mt-1 group-hover:text-indigo-600"
    >
        {icon.name}
    </div>
</div>
