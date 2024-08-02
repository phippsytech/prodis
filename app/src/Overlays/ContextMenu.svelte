<script>
import { ContextMenuStore } from './stores.js';
$: contextMenu  = $ContextMenuStore;

let contextMenuElement
let timeoutId

function closeContextMenu(){
    ContextMenuStore.set({ show: false });
}

function select(value){
    contextMenu.select(value);
    closeContextMenu()
}

function handleClick(event) {
    if(!contextMenuElement) {
        closeContextMenu()
        return;
    }
    if (!contextMenuElement.contains(event.target)) {
        closeContextMenu();
    }
}

$: {
    if(contextMenu.show){
        // debounce the click event to prevent it from firing immediately
        clearTimeout(timeoutId);
        window.removeEventListener('click', handleClick);
        timeoutId = setTimeout(() => window.addEventListener('click', handleClick), 300);
    }else{
        window.removeEventListener('click', handleClick);
    }
}


</script>

{#if contextMenu.show}
    <div bind:this={contextMenuElement} class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none" 
    style="left: {contextMenu.x}px; top: {contextMenu.y}px;"
    role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        <div class="py-1" role="none">
            <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-700" -->
            <span class="text-gray-700 font-medium block px-4 py-2 text-xs " role="menuitem" tabindex="-1" id="menu-item-0">{contextMenu.title}</span>

            {#each contextMenu.options as option}
                <span on:click={()=>select(option.value)} class="text-gray-700 block px-4 py-2 text-sm cursor-pointer hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{option.name}</span>
            {/each}
            
            <button on:click={()=>closeContextMenu()} class="text-gray-700 block w-full px-4 py-2 text-left text-sm" role="menuitem" tabindex="-1" id="menu-item-3">Close</button>
    
        </div>
    </div>
{/if}