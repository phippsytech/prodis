<script>
    import { SpinnerStore } from './stores.js';
    import {fade} from 'svelte/transition';
    $: spinner = $SpinnerStore;

</script>

<style>

#overlay {    
    z-index:10000;
    position: fixed;
    left:0;
    top:0;
    display: flex;
    align-items: center;
    justify-content: center;
    background:white;
    height: 100vh;
    width: 100vw
} 

.spinner {
    -webkit-animation: rotator 1.4s linear infinite;
    animation: rotator 1.4s linear infinite;
}

@-webkit-keyframes rotator {
0% {
transform: rotate(0deg);
}
100% {
transform: rotate(270deg);
}
}

@keyframes rotator {
0% {
transform: rotate(0deg);
}
100% {
transform: rotate(270deg);
}
}
.path {
stroke-dasharray: 187;
stroke-dashoffset: 0;
transform-origin: center;
-webkit-animation: dash 1.4s ease-in-out infinite, colors 5.6s ease-in-out infinite;
      animation: dash 1.4s ease-in-out infinite, colors 5.6s ease-in-out infinite;
}

@-webkit-keyframes colors {
0% {
stroke: #4285F4;
}
25% {
stroke: #DE3E35;
}
50% {
stroke: #F7C223;
}
75% {
stroke: #1B9A59;
}
100% {
stroke: #4285F4;
}
}

@keyframes colors {
0% {
stroke: #4285F4;
}
25% {
stroke: #DE3E35;
}
50% {
stroke: #F7C223;
}
75% {
stroke: #1B9A59;
}
100% {
stroke: #4285F4;
}
}
@-webkit-keyframes dash {
0% {
stroke-dashoffset: 187;
}
50% {
stroke-dashoffset: 46.75;
transform: rotate(135deg);
}
100% {
stroke-dashoffset: 187;
transform: rotate(450deg);
}
}
@keyframes dash {
0% {
stroke-dashoffset: 187;
}
50% {
stroke-dashoffset: 46.75;
transform: rotate(135deg);
}
100% {
stroke-dashoffset: 187;
transform: rotate(450deg);
}
}      </style>

{#if spinner.show}
<div id="overlay" transition:fade={{ duration: 150 }} class="flex flex-col items-center">
    <div id="spinner">
        <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
        </svg>
        
    </div>
    {#if spinner.message}<div class="text-gray-600 py-5">{spinner.message}</div>{/if}
</div>
{/if}