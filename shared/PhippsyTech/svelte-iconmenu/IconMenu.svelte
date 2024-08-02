<script>
    import { scale } from "svelte/transition";
    import { elasticOut } from "svelte/easing";
    import Icon from "./Icon.svelte";

    export let icons = [];
    export let userRoles = [];

    let iconList = [];

    function haveCommonElements(arr1, arr2) {
        // Check if both arrays are arrays
        if (Array.isArray(arr1) === false || Array.isArray(arr2) === false)
            return false;

        // Use the some() method to check if any elements in arr1 are also in arr2
        return arr1.some(function (element) {
            return arr2.includes(element);
        });
    }

    $: {
        iconList = icons.filter((icon) => {
            if (!icon.hasOwnProperty("role")) return true;
            if (userRoles != null) {
                if (haveCommonElements(icon.role, userRoles)) return true;
                // if(icon.role.some(function(element){
                //     if(userRoles.includes(icon.role)) return true;
                // })
            }
        });
    }
    // This article was a great resource for quickly getting the layout done. https://www.section.io/engineering-education/using-tailwind-css-grid-classes/
</script>

<!-- wrapping this with <div class="flex">...</div> will provide fixed width icons -->
<!-- <div class="flex"> -->
<div
    class="grid grid-cols-3
        xs:grid-cols-4 sm:grid-cols-6
        md:grid-cols-7 lg:grid-cols-9 xl:grid-cols-12
        gap-x-1 gap-y-0 text-slate-800"
>
    {#each iconList as icon, index}
        <div
            in:scale|global={{
                opacity: 0,
                start: 0.5,
                duration: 150,
                delay: index * 10,
            }}
        >
            <Icon {icon} />
        </div>
    {/each}
</div>
<!-- </div> -->
