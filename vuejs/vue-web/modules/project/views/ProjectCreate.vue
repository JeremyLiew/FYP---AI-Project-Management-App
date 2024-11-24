<template>
	<v-container>
		<v-row>
			<v-col cols="12">
				<h1 class="text-subtitle-1">Create New Project</h1>
			</v-col>
		</v-row>
		<v-form ref="projectForm">
			<!-- Project Name -->
			<v-row>
				<v-col cols="12" md="6">
					<v-text-field
						v-model="project.name"
						label="Project Name *"
						required
						:error-messages="errors.name"
					></v-text-field>
				</v-col>
			</v-row>
			<!-- Project Description -->
			<v-row>
				<v-col cols="12" md="6">
					<v-textarea
						v-model="project.description"
						label="Project Description"
						outlined
						:error-messages="errors.description"
					></v-textarea>
				</v-col>
			</v-row>
			<!-- Project Start Date -->
			<v-row>
				<v-col cols="12" md="6">
					<v-text-field
						v-model="project.start_date"
						label="Start Date *"
						type="date"
						:value="project.start_date"
						required
						outlined
						disabled
						:min="today"
					></v-text-field>
				</v-col>
			</v-row>
			<!-- Project End Date -->
			<v-row>
				<v-col cols="12" md="6">
					<v-text-field
						v-model="project.end_date"
						label="End Date *"
						type="date"
						required
						outlined
						:min="tomorrow"
						:error-messages="errors.end_date"
					></v-text-field>
				</v-col>
			</v-row>
			<!-- Project Status -->
			<v-row>
				<v-col cols="12" md="6">
					<v-select
						v-model="project.status"
						:items="statusOptions"
						label="Status *"
						required
						outlined
						disabled
					></v-select>
				</v-col>
			</v-row>
			<!-- Project Budget -->
			<v-row>
				<v-col cols="12" md="6">
					<v-select
						v-model="project.budget_id"
						:items="formattedBudgets"
						item-title="formattedName"
						item-value="id"
						label="Budget *"
						required
						outlined
						:error-messages="errors.budget_id"
					></v-select>
				</v-col>
			</v-row>
			<!-- Submit Button -->
			<v-row>
				<v-col cols="12" style="text-align: end;">
					<v-btn
						color="primary"
						@click="submitProject"
					>
						Create Project
					</v-btn>
				</v-col>
			</v-row>
		</v-form>
	</v-container>
</template>

<script>
import ProjectClient from "../client";

export default {
	data() {
		return {
			project: {
				name: "",
				description: "",
				start_date: "",
				end_date: "",
				status: "Pending",
				budget_id: null,
			},
			budgets: [
				{id: 1, name: 'Web', amount: 2000},
				{id: 2, name: 'App', amount: 7000}
			],
			statusOptions: ["Ongoing", "Completed", "Pending"],
			today: "",
			tomorrow: "",
			errors: {},
		};
	},
	computed: {
		startDate() {
			return this.project.start_date || this.today;
		},
		formattedBudgets() {
			return this.budgets.map(budget => ({
				...budget,
				formattedName: `${budget.name} - RM${budget.amount.toLocaleString()}`
			}));
		}
	},
	mounted() {
		const now = new Date();
		const localYear = now.getFullYear();
		const localMonth = String(now.getMonth() + 1).padStart(2, '0');
		const localDay = String(now.getDate()).padStart(2, '0');

		this.today = `${localYear}-${localMonth}-${localDay}`;
		this.project.start_date = this.today;
		this.tomorrow = new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().split("T")[0];
		this.fetchBudgets();
	},
	methods: {
		// Fetch available budgets from the database
		fetchBudgets() {
			// Example API call to fetch available budgets
			// ProjectClient.get("budgets")
			// 	.then((response) => {
			// 		this.budgets = response.data;
			// 	})
			// 	.catch((error) => {
			// 		console.error("Error fetching budgets", error);
			// 	});
		},
		// Submit project creation form
		submitProject() {
			this.errors = {}
			ProjectClient.createProject(this.project)
				.then((response) => {
					console.log("Project created successfully", response);
					this.$refs.projectForm.reset();
					this.$router.push({ name: "project-listings-page" , query: { success: "Project created successfully!" },});
				})
				.catch((error) => {
					console.error("Error creating project", error);
					if (error.response && error.response.data.errors) {
						this.errors = error.response.data.errors;
					}
				});
		},
	},
};
</script>
