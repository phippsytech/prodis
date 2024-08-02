<script>
    import Trix from '@app/../node_modules/trix';
    // import Trix from 'trix';

    export let value

    // document.addEventListener("trix-before-initialize", () => {
    //     // Change Trix.config if you need
    // })
</script>



<input id="x" type="hidden" name="content" bind:value={value} >
  <!-- <trix-editor input="x"></trix-editor> -->