<script>
    import { TooltipStore } from './stores.js';
    import { fade, scale } from 'svelte/transition';

    $: Tooltip  = $TooltipStore;
    
    let TooltipElement
    let isHovering = false; // Local state to track hover status
        
     // Function to call when mouse enters the TooltipElement
     function handleMouseLeave() {
        isHovering=false;
        Tooltip.show = false;
        // TooltipStore.set({ show: false });
    }

    function handleClick(){
        if(Tooltip.select){
            Tooltip.select();
        }
    }

    function toSolidColor(hexColor) {
  // Check if the hex color includes an alpha value by checking its length
  if (hexColor.length === 9) {
    // Return the first 6 characters, removing the alpha value
    return hexColor.substring(0, 7);
  }
  // If the hex color doesn't include an alpha value or is not in the expected format,
  // return it as is (assuming it's already a solid color)
  return hexColor;
}


    // Combine show, x, and y into a reactive key
    $: tooltipKey = `${Tooltip.show}-${Tooltip.x}-${Tooltip.y}`;

    </script>
    
    
        
        {#if Tooltip.show}
        <!-- {#key tooltipKey} -->
            <div bind:this={TooltipElement} 
            out:fade={{delay: 300, duration: 0 }}
            
            on:mouseleave={handleMouseLeave} 
            on:click={handleClick}
            class="absolute left-0 z-10 mt-0 ml-0  rounded-md  shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none cursor-pointer hover:scale-125 transition ease-in-out " 
            style="left: {Tooltip.x}px; top: {Tooltip.y}px; width: {Tooltip.width}px; min-height: {Tooltip.height}px; background-color: {toSolidColor(Tooltip.backgroundColor)}; "
            role="tooltip" tabindex="-1">
                <div class="py-1 " role="none">
                    <span class="text-black font-medium block px-4 py-2 text-xs "  tabindex="-1" id="menu-item-0">{@html Tooltip.title}</span>
                </div>
            </div>
            <!-- {/key} -->
            {/if}
            
    