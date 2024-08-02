<script>
    import SessionList from "./SessionList.svelte";

    export let params
    
    let staff_id = params.staff_id;

</script>

<SessionList  {staff_id} billed={true} />
