<template>
		<div class="charts-container">
			<div class="chart-item">
      <h3>Expenses for Project {{ projectId }}</h3>
      <canvas id="expenseChart"></canvas>
    </div>
    <div class="chart-item">
      <h3>Task Completion Time for Project {{ projectId }}</h3>
      <canvas id="taskCompletionChart"></canvas>
    </div>
    <div class="chart-item">
      <h3>Task Status Over Time for Project {{ projectId }}</h3>
      <canvas id="taskStatusChart"></canvas>
    </div>  

    <!-- New buttons for download -->
    <div class="download-buttons">
      <button @click="downloadProjectDetails('txt')" class="btn-primary">Download as TXT</button>
      <button @click="downloadProjectDetails('csv')" class="btn-primary">Download as CSV</button>
    </div>
</div>
</template>

<script>
    import ReportClient from "../client";
    import { Chart, DoughnutController, ArcElement, BarController, BarElement, Tooltip, Legend, CategoryScale, LinearScale } from 'chart.js';
    Chart.register(DoughnutController, ArcElement,BarController, BarElement, Tooltip, Legend, CategoryScale, LinearScale);

    export default {
      data() {
        return {
          projectId: 1, 
          expenses: [],
          tasks: [],
          project: null, // Store project details here
        };
      },
      mounted() {
        this.projectId = this.$route.params.id;
        this.fetchProjectExpenses();
        this.fetchProjectTasks();
        this.fetchTaskStatus();
        this.fetchProject();
      },
      methods: {
        fetchProjectExpenses() {
          ReportClient.fetchProjectExpenses(this.projectId)
            .then((response) => {
              this.expenses = response.data;
              this.renderChart();
            })
            .catch((error) => {
              console.error("Error fetching expenses:", error);
            });
        },
        renderChart() {
          const labels = this.expenses.map((expense) => expense.name);
          const data = this.expenses.map((expense) => expense.amount);

          const ctx = document.getElementById("expenseChart").getContext("2d");

          new Chart(ctx, {
            type: "bar", // Change chart type to 'bar'
            data: {
              labels: labels,
              datasets: [
                {
                  label: "Expenses Over Time",
                  data: data,
                  backgroundColor: "#42a5f5", // Use a fill color for the bars
                  borderColor: "#1e88e5", // Optional: border color for the bars
                  borderWidth: 1, // Optional: border width for the bars
                },
              ],
            },
            options: {
              responsive: true,
              scales: {
                x: {
                  title: {
                    display: true,
                    text: "Expense Name",
                    color: "white", // White text for labels on dark background
                  },
                  grid: {
                    color: "rgba(255, 255, 255, 0.1)", // Lighter grid lines
                  },
                },
                y: {
                  title: {
                    display: true,
                    text: "Amount Spent",
                    color: "white",
                  },
                  grid: {
                    color: "rgba(255, 255, 255, 0.1)",
                  },
                },
              },
              plugins: {
                tooltip: {
                  backgroundColor: "rgba(0, 0, 0, 0.7)", // Dark tooltip background
                  titleColor: "white", // Tooltip title in white
                  bodyColor: "white", // Tooltip text color in white
                },
                legend: {
                  labels: {
                    color: "white", // Legend labels in white
                  },
                },
              },
            },
          });
        },
        fetchProjectTasks() {
          ReportClient.fetchProjectTasks(this.projectId)
            .then((response) => {
              this.tasks = response.data;
              this.renderTaskCompletionChart();
              this.renderTaskStatusChart();
            })
            .catch((error) => {
              console.error("Error fetching tasks:", error);
            });
        },
        renderTaskCompletionChart() {
          const labels = this.tasks.map((task) => task.name);
          const data = this.tasks.map((task) => task.completion_time);

          const ctx = document.getElementById("taskCompletionChart").getContext("2d");

          new Chart(ctx, {
            type: "bar", // Change to 'bar' for horizontal bars
            data: {
              labels: labels,
              datasets: [
                {
                  label: "Task Time (hours)",
                  data: data,
                  backgroundColor: "#ff6384", // Custom color
                  borderColor: "#ff6384", // Border color (optional)
                  borderWidth: 1, // Border width (optional)
                },
              ],
            },
            options: {
              responsive: true,
              indexAxis: "y", // This makes the bar chart horizontal
              scales: {
                x: {
                  title: {
                    display: true,
                    text: "Task Time (hours)",
                    color: "white",
                  },
                  grid: {
                    color: "rgba(255, 255, 255, 0.1)",
                  },
                },
                y: {
                  title: {
                    display: true,
                    text: "Task Name",
                    color: "white",
                  },
                  grid: {
                    color: "rgba(255, 255, 255, 0.1)",
                  },
                },
              },
              plugins: {
                tooltip: {
                  backgroundColor: "rgba(0, 0, 0, 0.7)",
                  titleColor: "white",
                  bodyColor: "white",
                },
                legend: {
                  labels: {
                    color: "white",
                  },
                },
              },
            },
          });
        },
        fetchTaskStatus() {
          ReportClient.fetchTaskStatus(this.projectId) // Adjusted to call the correct method for fetching task status
            .then((response) => {
              const tasks = response.data;
              this.renderTaskStatusChart(tasks); // Pass tasks to the render method
            })
            .catch((error) => {
              console.error("Error fetching tasks:", error);
            });
        },
        renderTaskStatusChart(tasks) {
          const statusCounts = {
            Pending: 0,
            Ongoing: 0,
            Completed: 0,
          };

          // Count the occurrences of each status
          tasks.forEach((task) => {
            if (statusCounts[task.status] !== undefined) {
              statusCounts[task.status]++;
            }
          });

          const data = {
            labels: ["Pending", "Ongoing", "Completed"],
            datasets: [
              {
                label: "Task Status",
                data: [
                  statusCounts.Pending,
                  statusCounts.Ongoing,
                  statusCounts.Completed,
                ],
                backgroundColor: ["#ff6384", "#36a2eb", "#4caf50"], // Colors for the statuses
              },
            ],
          };

          const ctx = document.getElementById("taskStatusChart").getContext("2d");

          new Chart(ctx, {
            type: "doughnut", // Doughnut chart type
            data: data,
            options: {
              responsive: true,
              plugins: {
                legend: {
                  position: "top",
                  labels: {
                    color: "white", // Change legend text color to white
                  },
                },
                tooltip: {
                  callbacks: {
                    label: function (tooltipItem) {
                      return `${tooltipItem.label}: ${tooltipItem.raw} tasks`;
                    },
                  },
                  backgroundColor: "rgba(0, 0, 0, 0.7)",
                  titleColor: "white",
                  bodyColor: "white",
                },
              },
              cutout: "70%", // Adjusts the size of the inner cutout to create a doughnut
            },
          });
        },
        fetchProject() {
          ReportClient.fetchProject(this.projectId) // Adjust to call the correct API method for fetching project data
            .then((response) => {
              this.project = response.data; // Save the project data
            })
            .catch((error) => {
              console.error("Error fetching project:", error);
            });
        },
        downloadProjectDetails(format) {
          // Call backend to generate the requested file
          const payload = {
            format: format,
            projectId: this.projectId,
          };
          ReportClient.downloadProjectDetails(payload)
            .then((response) => {
              const blob = new Blob([response.data], { type: response.headers['content-type'] });
              const link = document.createElement('a');
              link.href = URL.createObjectURL(blob);
              link.download = `project_${this.project.name}_details.${format}`;
              link.click();
            })
            .catch((error) => {
              console.error("Error downloading project details:", error);
            });
        },
      },
    };
</script>

<style scoped>
.charts-container {
	display: flex;
	justify-content: space-between;
	gap: 20px;
	flex-wrap: wrap;
}

.chart-item {
	flex: 1 1 30%;
	min-width: 300px;
	box-sizing: border-box;
	padding: 10px;
	border: 1px solid #444;
	border-radius: 8px;
	background-color: #2e2e2e; /* Dark background for charts */
}

canvas {
	width: 100%;
	height: 300px;
	background-color: #1e1e1e; /* Dark background for the chart */
	border-radius: 8px;
}

.btn-primary {
	background-color: #007bff; /* Light blue button */
}

.btn-primary:hover {
	background-color: #0056b3;
}

.download-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
  }
</style>
