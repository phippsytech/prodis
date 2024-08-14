<script>
    //TODO: replace with actual data, inquire to sir phippsy
    import { onMount } from 'svelte';
    import { Chart, LineElement, LineController, CategoryScale, LinearScale, PointElement, Filler } from 'chart.js';
  
    Chart.register(LineElement, LineController, CategoryScale, LinearScale, PointElement, Filler);
  
    let chart;
  
    onMount(() => {
      const ctx = document.getElementById('capacityChart').getContext('2d');
  
      chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'], // Replace with your weeks
          datasets: [{
            label: 'Hours Worked',
            data: [35, 40, 50, 45, 60], // Replace with your actual data
            fill: true, // This fills the area under the curve
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4 // Makes the line curved
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
              max: 80 // Adjust the max value as needed for your capacity
            }
          },
          plugins: {
            annotation: {
              annotations: {
                maxCapacityLine: {
                  type: 'line',
                  yMin: 50, // Replace with your max capacity
                  yMax: 50, // Same as above, it creates a horizontal line
                  borderColor: 'rgba(255, 99, 132, 1)',
                  borderWidth: 2,
                  label: {
                    content: 'Max Capacity',
                    enabled: true,
                    position: 'end',
                  }
                }
              }
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