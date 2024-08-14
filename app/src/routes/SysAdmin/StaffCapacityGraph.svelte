<script>
  import { onMount, onDestroy } from 'svelte';
  import { Chart, LineElement, LineController, CategoryScale, LinearScale, PointElement, Filler } from 'chart.js';

  // Register the necessary components
  Chart.register(LineElement, LineController, CategoryScale, LinearScale, PointElement, Filler);

  export let labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']; // Default labels
  export let datasets = []; 
  let chart;

  onMount(() => {
    const ctx = document.getElementById('capacityChart').getContext('2d');

    chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels, 
        datasets: datasets 
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            max: 80 // Adjust the max value as needed for your capacity
          }
        }
      }
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

<style>
  /* Add any custom styles for your chart here */
</style>
