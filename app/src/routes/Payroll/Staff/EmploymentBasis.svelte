<script>
    export let value="CASUAL"
</script>


<select class="p-1" bind:value={value}>
    <option value="SALARY">Salary</option>
    <option value="FULLTIME">Full-time</option>
    <option value="PARTTIME">Part-time</option>
    <option value="CASUAL">Casual</option>
</select>