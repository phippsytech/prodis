<script>
  	import { onMount } from 'svelte';

    function invertHex(hex) {
        return (Number(`0x1${hex}`) ^ 0xFFFFFF).toString(16).substr(1).toUpperCase()
    }

    let spinner_color;
    let spinner_color_light;

    onMount(() => {
        let theme_color="ffffff"; // default theme color is white
        try{
            theme_color=document.querySelector('meta[name="theme-color"]').content.substring(1);
        }catch (error){
            // just catching the error to avoid error messages         
        }
        spinner_color = "#"+invertHex(theme_color);
        spinner_color_light = spinner_color+"66";
    });

</script>

<style>

    #loading {
        display: inline-block;
        width: 50px;
        height: 50px;
        border: 3px solid var(--spinner-color-light);
        border-top-color: var(--spinner-color);
        border-radius: 50%;
        animation: spin 1s ease-in-out infinite;
        -webkit-animation: spin 1s ease-in-out infinite;
      }

      @keyframes spin {
        to { -webkit-transform: rotate(360deg); }
      }

      @-webkit-keyframes spin {
        to { -webkit-transform: rotate(360deg); }
      }

</style>

<div id="loading" style="--spinner-color: #{spinner_color}; --spinner-color-light: {spinner_color_light};"></div>
