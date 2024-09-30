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

  // Trigger the native date picker when the formatted date is clicked
  function openDatePicker() {
    document.getElementById("hiddenDatePicker").click();
  }
</script>

<!-- Display the formatted date or prompt user to select a date -->
<p on:click={openDatePicker} style="cursor: pointer;">
  {selectedDate ? formatDate(selectedDate) : "Select a date"}
</p>

<!-- Hidden date input, used to trigger the native date picker -->
<input
  type="date"
  id="hiddenDatePicker"
  bind:value={selectedDate}
  on:change={handleDateChange}
  style="display: none;"
/>
