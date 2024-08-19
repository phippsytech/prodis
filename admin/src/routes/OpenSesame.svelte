<script>
    import { jspa } from "@shared/jspa.js";
    import { push } from "svelte-spa-router";
    jspa("/User", "enableAdminLogin", {}).finally(push.bind(null, "/"));
</script>
