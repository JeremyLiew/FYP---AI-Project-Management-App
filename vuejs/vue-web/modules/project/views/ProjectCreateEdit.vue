<template>
	<v-container>
		<v-row>
			<v-col cols="12">
				<h1 class="text-subtitle-1">{{ isEdit ? "Edit Project" : "Create New Project" }}</h1>
			</v-col>
		</v-row>

		<v-skeleton-loader v-if="modelLoading" type="article"></v-skeleton-loader>

		<v-form v-else ref="projectForm">
			<!-- Project Name -->
			<v-row>
				<v-col cols="12">
					<v-text-field
						v-model="project.name"
						label="Project Name *"
						required
						:error-messages="errors.name"
					></v-text-field>
				</v-col>
				<!-- Project Description -->
				<v-col cols="12">
					<v-textarea
						v-model="project.description"
						label="Project Description"
						outlined
						:error-messages="errors.description"
					></v-textarea>
				</v-col>
				<!-- Project Start Date -->
				<v-col cols="12" md="6">
					<v-text-field
						v-model="project.start_date"
						label="Start Date *"
						type="date"
						required
						outlined
						disabled
						:min="today"
					></v-text-field>
				</v-col>
				<!-- Project End Date -->
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
				<!-- Project Status -->
				<v-col cols="12" md="6">
					<v-select
						v-model="project.status"
						:items="statusOptions"
						label="Status *"
						required
						outlined
						:disabled="isEdit?false:true"
					></v-select>
				</v-col>
				<!-- Project Budget -->
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
						depressed
						:loading="isLoading"
						@click="isEdit ? updateProject() : submitProject()"
					>
						{{ isEdit ? "Update Project" : "Create Project" }}
					</v-btn>
				</v-col>
			</v-row>
		</v-form>
	</v-container>
</template>

<script>
import ProjectClient from "../client";

export default {
	props: {
		isEdit: {
			type: Boolean,
			default: false,
		},
	},
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
			isLoading: false,
			modelLoading: true,
		};
	},
	computed: {
		formattedBudgets() {
			return this.budgets.map((budget) => ({
				...budget,
				formattedName: `${budget.name} - RM${budget.amount.toLocaleString()}`,
			}));
		},
	},
	mounted() {
		this.initializeDates();
		this.fetchBudgets();
		if (this.isEdit) {
			const projectId = this.$route.params.id;
			if (projectId) {
				this.fetchProject(projectId);
			}
		}
	},
	methods: {
		initializeDates() {
			const now = new Date();
			const localYear = now.getFullYear();
			const localMonth = String(now.getMonth() + 1).padStart(2, '0');
			const localDay = String(now.getDate()).padStart(2, '0');

			this.today = `${localYear}-${localMonth}-${localDay}`;
			this.project.start_date = this.today;
			this.tomorrow = new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().split("T")[0];
		},
		fetchBudgets() {
			// API call to fetch budgets (if needed)
		},
		submitProject() {
			this.isLoading = true;
			this.errors = {};
			ProjectClient.createProject(this.project)
				.then((response) => {
					this.isLoading = false
					console.log("Project created successfully", response);
					this.$toast.success("Project created successfully")
					setTimeout(() => {
						this.$refs.projectForm.reset();
						this.$router.push({ name: "project-listings-page" });
					}, 500);
				})
				.catch((error) => {
					console.error("Error saving project", error);
					if (error.response && error.response.data.errors) {
						this.errors = error.response.data.errors;
					}
				}).finally(() => {
					this.isLoading = false
				});
		},
		updateProject() {
			this.model_loading = true;
			this.errors = {};
			const projectId = this.$route.params.id;
			ProjectClient.updateProject({ id: projectId, ...this.project })
				.then((response) => {
					this.isLoading = false
					console.log("Project updated successfully", response);
					this.$toast.success("Project updated successfully")
					setTimeout(() => {
						this.$router.push({ name: "project-listings-page" });
					}, 500);
				})
				.catch((error) => {
					console.error("Error updating project", error);
					if (error.response && error.response.data.errors) {
						this.errors = error.response.data.errors;
					}
				}).finally(() => {
					this.isLoading = false
				});
		},
		fetchProject(id){
			this.modelLoading = true;
			ProjectClient.fetchProject(id)
				.then((response) => {
					this.project = response.data.project;
				})
				.catch((error) => {
					console.error("Error fetching project:", error);
					this.$toast.error("Failed to fetch project details.");
				})
				.finally(() => {
					this.modelLoading = false;
				});
		}
	},
};
</script>
