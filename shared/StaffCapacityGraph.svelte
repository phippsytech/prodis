<script>
  import { onMount, onDestroy } from "svelte";
  import {
    Chart,
    LineElement,
    LineController,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler,
  } from "chart.js";

  // Register the necessary components
  Chart.register(
    LineElement,
    LineController,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler,
  );

  export let labels = ["Week 1", "Week 2", "Week 3", "Week 4", "Week 5"]; // Default labels
  export let datasets = [];
  let chart;

  onMount(() => {
    const ctx = document.getElementById("capacityChart").getContext("2d");

    chart = new Chart(ctx, {
      type: "line",
      data: {
        labels: labels,
        datasets: datasets,
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            max: 80, // Adjust the max value as needed for your capacity
          },
        },
      },
    });
  });

  // Cleanup chart on component destroy
  onDestroy(() => {
    if (chart) {
      chart.destroy();
    }
  });
</script>

<canvas id="capacityChart" width="400" height="200"></canvas>

<!-- EXAMPLE USAGE

<script>
    let labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];

    let datasets = [
    {
      label: 'Hours Worked',
      data: [60, 50, 40, 30, 20], // Your custom data for Hours Worked
      fill: true,
      borderColor: 'rgba(75, 192, 192, 1)',
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      tension: 0.1, // Makes the line curved
      order: 2 // Draw this line below the max capacity line
    },
    {
      label: 'Max Capacity',
      data: [30, 30, 30, 30, 30], // Your custom data for Max Capacity
      borderColor: 'rgba(255, 99, 132, 1)', // Red color for the max capacity line
      borderWidth: 2,
      pointRadius: 0, // No points at the data points
      fill: false, // Do not fill the area under this line
      tension: 0, // Straight line
      order: 1 // Draw this line above the actual data line
    }
  ];
</script>

<StaffCapacityGraph {labels} {datasets}/>

-->

<style>
  /* Add any custom styles for your chart here */
</style>
