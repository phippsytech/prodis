<script>
  let selectedDate = ""; // The currently selected date

  // Function to format the date as d MMM YYYY
  function formatDate(date) {
    const options = { day: "numeric", month: "short", year: "numeric" };
    return new Date(date).toLocaleDateString("en-GB", options);
  }

  // Function to handle date selection
  function handleDateChange(event) {
    selectedDate = event.target.value;
  }
</script>

<!-- Display the formatted date or "Set date" if no date is selected -->
<p
  on:click={() => document.getElementById("hiddenDatePicker").click()}
  style="cursor: pointer;"
>
  {selectedDate ? formatDate(selectedDate) : "Set date"}
</p>

<!-- Hidden native date input used to trigger the date picker -->
<input
  type="date"
  id="hiddenDatePicker"
  bind:value={selectedDate}
  on:change={handleDateChange}
/>
