<script>
  import { onMount } from "svelte";
  import { websocketStore } from "./websocketStore.js";

  onMount(() => {
    websocketStore.connect();
  });
</script>
