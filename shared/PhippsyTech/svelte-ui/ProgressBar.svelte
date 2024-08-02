<script>

    export let value=0
    $: style = `width: ${value}%; transition: width 0.5s ease;`;
    
</script>

<style>

    .barberpole {
      background-image: 
        repeating-linear-gradient(
          27.5deg, 
          transparent, 
          transparent 1rem,
        #e5e7eb 1rem,
        #d1d5db 2rem
          
        );
      background-size: 200% 200%;
      background-position: 100% 100%;
      animation: barberpoleKeyframes 12s linear infinite;
    }
    
    @keyframes barberpoleKeyframes {
      100% {
        background-position: -100% 100%;
      }
    }
    
</style>
    

<div class="w-full bg-gray-200 rounded-full barberpole">
    <div class="bg-indigo-600 text-xs font-bold text-white text-center p-0.5 leading-none rounded-full " style={style}>
        <span style="filter: drop-shadow(1px 1px 2px rgb(0 0 0 / 0.75))">{value}%</span>
    </div>
</div>
