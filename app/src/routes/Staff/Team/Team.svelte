<script>
  import Container from "@shared/Container.svelte";
  import ReportsTo from "./ReportsTo.svelte";
  import Supervises from "./Supervises.svelte";

  export let params;

  let staff_id = params.staff_id;
</script>

<div>
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Team Relationship
  </div>

  <div>
    Manage who this staff member supervises and who this staff member reports
    to. A staff member can be supervised by multiple staff, and report to
    multiple staff. Supervisors have access to the participants of the staff
    they manage.
  </div>
</div>

<Container>
  <Supervises bind:supervisor_id={staff_id} />
  <ReportsTo bind:staff_id />
</Container>
