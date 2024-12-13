<template>
	<div>

		<h3>Projects</h3>
		<table v-if="projects.length > 0" class="table table-dark table-bordered">
			<thead>
				<tr>
					<th>Project Name</th>
					<th>Project Description</th>
					<th>Project Status</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="project in projects" :key="project.id">
					<td>{{ project.name }}</td>
					<td>{{ project.description }}</td>
					<td :style="{ color: getStatusColor(project.status) }">
						{{ project.status }}
					</td>
					<td>
						<button 
							@click="openProjectDetail(project.id)" 
							class="btn btn-sm btn-primary"
						>
							<i class="fas fa-eye"></i> View
						</button>
					</td>
				</tr>
			</tbody>
		</table>
		<p v-else>No projects found</p>

		<h3>Tasks</h3>
		<table v-if="tasks.length > 0" class="table table-dark table-bordered">
			<thead>
				<tr>
					<th>Task Name</th>
					<th>Task Description</th>
					<th>Task Status</th>
					<th>Task Priority</th>
					<th>Associated Project</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="task in tasks" :key="task.id">
					<td>{{ task.name }}</td>
					<td>{{ task.description }}</td>
					<td :style="{ color: getStatusColor(task.status) }">
						{{ task.status }}
					</td>
					<td :style="{ color: getPriorityColor(task.priority) }">
						{{ task.priority }}
					</td>
					<td>{{ getProjectName(task.project_id) }}</td>
				</tr>
			</tbody>
		</table>
		<p v-else>No tasks found</p>

		<div class="charts-container">
			<div class="chart-item">
				<h3>Expenses by you</h3>
				<canvas id="expenseChart"></canvas>
			</div>
			
			<div class="chart-item">
				<h3>Performance by Project</h3>
				<canvas id="projectPerformanceChart"></canvas>
			</div>
			
			<div class="chart-item">
				<h3>Performance by Task</h3>
				<canvas id="taskPerformanceChart"></canvas>
			</div>
		</div>
	</div>
</template>

<script>
import ReportClient from "../client";
import { Chart, PieController, ArcElement, Tooltip, Legend } from "chart.js";
Chart.register(PieController, ArcElement, Tooltip, Legend);

export default {
	data() {
		return {
			aiFeedback: null, 
			projects: [],
			tasks: [],
			userId: null,
			modelLoading: true,
			hasData: true,
		};
	},
	mounted() {
		this.userId = this.$auth.user().user.id;
		this.fetchProjectsAndTasks();
		this.fetchExpenseCategoryData();
		this.fetchPerformanceData();
	},
	methods: {
		fetchProjectsAndTasks() {
			this.modelLoading = true;
			this.hasData = true;

			const payload = {
				userId: this.userId,
				searchQuery: "",
				selectedFilter: null,
				page: 1,
				itemsPerPage: 10,
			};
			ReportClient.getProjectsAndTasks(payload)
				.then((response) => {
					this.projects = response.data.projects;
					this.tasks = response.data.tasks;
					this.hasData = this.projects.length > 0 || this.tasks.length > 0;
				})
				.catch((error) => {
					console.error("Error fetching projects and tasks:", error);
					this.hasData = false;
				})
				.finally(() => {
					this.modelLoading = false;
				});
		},
		getStatusColor(status) {
			const colors = {
				Pending: "red",
				Ongoing: "orange",
				Completed: "green",
			};
			return colors[status];
		},
		getPriorityColor(priority) {
			const colors = {
				High: "red",
				Medium: "blue",
				Low: "green",
			};
			return colors[priority];
		},
		getProjectName(projectId) {
			const project = this.projects.find((p) => p.id === projectId);
			return project ? project.name : "Unknown Project";
		},
		openProjectDetail(projectId) {
			this.$router.push({ name: "team-report-page", params: { id: projectId } });
		},
		fetchExpenseCategoryData() {
			const payload = {
				userId: this.userId,
			};

			ReportClient.getExpenseCategoryData(payload)  // Use the updated endpoint
				.then((response) => {
					const rawData = response.data;
					console.log(rawData);

					// Group expenses by expense_name
					const groupedExpenses = rawData.reduce((acc, expense) => {
						const expenseName = expense.expense_name;  // Group by expense_name
						
						if (!acc[expenseName]) {
							acc[expenseName] = {
								expenseName: expense.expense_name,  // Expense name
								totalAmount: 0
							};
						}

						// Add the expense amount to the total for this expense_name
						acc[expenseName].totalAmount += parseFloat(expense.expense_value); // Using 'expense_value'

						return acc;
					}, {});

					// Extract the labels (expense names) and the summed amounts
					const expenseNames = Object.values(groupedExpenses).map((group) => group.expenseName);
					const summedAmounts = Object.values(groupedExpenses).map((group) => group.totalAmount);

					this.renderExpenseChart(expenseNames, summedAmounts);  // Render the chart with aggregated data
				})
				.catch((error) => {
					console.error("Error fetching expenses:", error);
				});
		},

		renderExpenseChart(expenseNames, expenseValues) {
			const ctx = document.getElementById("expenseChart").getContext("2d");
			new Chart(ctx, {
				type: "doughnut",  // Doughnut chart type
				data: {
					labels: expenseNames,  // Expense names as labels
					datasets: [
						{
							data: expenseValues,  // Sum of expenses for each category
							backgroundColor: expenseNames.map(() => this.getRandomColor()),  // Random color for each segment
						},
					],
				},
				options: {
					responsive: true,
					cutoutPercentage: 50,  // Adjust the size of the hole in the doughnut chart
					plugins: {
						legend: {
							position: "top",  // Position of the legend
						},
						tooltip: {
							callbacks: {
								label: function (tooltipItem) {
									let value = tooltipItem.raw;
									value = Number(value);  // Ensure value is a number

									if (isNaN(value)) {
										return `${tooltipItem.label}: Invalid Value`;
									}

									// Format value as currency (RM)
									const formattedValue = `RM ${value >= 1000 ? value.toFixed(4) : value.toFixed(2)}`;
									return `${tooltipItem.label}: ${formattedValue}`;
								},
							},
						},
					},
				},
			});
		},

		// Fetch performance data for Project and Task
		fetchPerformanceData() {
			const payload = {
				userId: this.userId,
			};
		ReportClient.getPerformanceData(payload)
			.then((response) => {
			const projectData = response.data.projects;
			const taskData = response.data.tasks;
			console.log(projectData);

			this.renderProjectPerformanceChart(projectData);
			this.renderTaskPerformanceChart(taskData);
			})
			.catch((error) => {
			console.error("Error fetching performance data:", error);
			});
		},
		// Render the project performance chart (Pie Chart)
		renderProjectPerformanceChart(projectData) {
		const labels = projectData.map((data) => data.name);  // Project names as labels
		const dataset = {
			label: "Project Performance",
			data: projectData.map((data) => data.completionRate === 'Good' ? 100 : 50), // Assuming completionRate is 'Good' or 'Poor'
			backgroundColor: projectData.map(() => this.getRandomColor()),  // Random color for each section
		};

			const ctx = document.getElementById("projectPerformanceChart").getContext("2d");
			new Chart(ctx, {
				type: 'pie',  // Change to Pie chart
				data: {
				labels,
				datasets: [dataset],
				},
				options: {
				responsive: true,
				plugins: {
					tooltip: {
					callbacks: {
						label: (context) => {
						// Get the current label index
						const index = context.dataIndex;
						const project = projectData[index];

						// Custom tooltip content
						return `${project.name} - Status: ${project.status} - Rating: ${project.completionRate}`;
						}
					}
					}
				}
				},
			});
		},
		// Render the task performance chart (Pie Chart)
		renderTaskPerformanceChart(taskData) {
		const labels = taskData.map((data) => data.name);  // Task names as labels
		const dataset = {
			label: "Task Performance",
			data: taskData.map((data) => data.completionRate === 'Good' ? 100 : 50),// Time spent on each task
			backgroundColor: taskData.map(() => this.getRandomColor()),  // Random color for each section
		};

			const ctx = document.getElementById("taskPerformanceChart").getContext("2d");
			new Chart(ctx, {
				type: 'pie',  // Change to Pie chart
				data: {
				labels,
				datasets: [dataset],
				},
				options: {
				responsive: true,
				plugins: {
					tooltip: {
					callbacks: {
						label: (context) => {
						// Get the current label index
						const index = context.dataIndex;
						const task = taskData[index];

						// Custom tooltip content
						return `${task.name} - Status: ${task.status} - Rating: ${task.completionRate} `;
						}
					}
					}
				}
				},
			});
		},
		getRandomColor() {
			const letters = "0123456789ABCDEF";
			let color = "#";
			for (let i = 0; i < 6; i++) {
				color += letters[Math.floor(Math.random() * 16)];
			}
			return color;
		},
	},
};
</script>

<style scoped>
body {
	background-color: #121212; /* Dark background for the whole page */
	color: white; /* Light text */
}

.table-dark {
	background-color: #1c1c1c; /* Dark background for tables */
	color: white; /* Light text for table */
}

.thead-dark {
	background-color: #333; /* Dark header background */
}

.thead-light {
	background-color: #444; /* Slightly lighter header background */
}

.table-bordered {
	border: 1px solid #444; /* Dark borders */
}

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
</style>
