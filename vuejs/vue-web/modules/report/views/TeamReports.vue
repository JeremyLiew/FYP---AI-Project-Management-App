<template>
  <div>
    <h3>Expenses for Project {{ projectId }}</h3>
    <canvas id="expenseChart"></canvas>

    <h3>Task Completion Time for Project {{ projectId }}</h3>
    <canvas id="taskCompletionChart"></canvas>

    <h3>Task Status for Project {{ projectId }}</h3>
    <canvas id="taskStatusChart"></canvas>
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
        };
      },
      mounted() {
        this.projectId = this.$route.params.id;
        this.fetchProjectExpenses();
        this.fetchProjectTasks();
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
                  label: "Task Completion Time (hours)",
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
                    text: "Completion Time (hours)",
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
        renderTaskStatusChart() {
          const statuses = this.tasks.map((task) => task.status);
          const statusCounts = {
            Pending: 0,
            InProgress: 0,
            Completed: 0,
          };

          statuses.forEach((status) => {
            if (statusCounts[status] !== undefined) {
              statusCounts[status]++;
            }
          });

          const data = {
            labels: ["Pending", "InProgress", "Completed"],
            datasets: [
              {
                label: "Task Status",
                data: [
                  statusCounts.Pending,
                  statusCounts.InProgress,
                  statusCounts.Completed,
                ],
                backgroundColor: ["#ff6384", "#36a2eb", "#4caf50"], // Colors for the statuses
              },
            ],
          };

          const ctx = document.getElementById("taskStatusChart").getContext("2d");

          new Chart(ctx, {
            type: "doughnut", // Change to 'doughnut' for circular chart
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
      },
    };
</script>

<style scoped>
canvas {
  width: 100%;
  height: 400px;
  background-color: #333; /* Dark background for the chart */
  border-radius: 8px; /* Optional: Adds rounded corners */
}
h3 {
  color: white; /* Set heading text color to white */
}
</style>
