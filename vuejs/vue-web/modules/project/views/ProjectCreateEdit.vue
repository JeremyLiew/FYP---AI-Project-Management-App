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
				<v-col cols="12" md="4">
					<v-select
						v-model="project.status"
						:items="statusOptions"
						label="Status *"
						required
						outlined
						:disabled="isEdit?false:true"
					></v-select>
				</v-col>
				<!-- Project Priority -->
				<v-col cols="12" md="4">
					<v-select
						v-model="project.priority"
						:items="priorityOptions"
						label="Priority"
						required
						outlined
						:error-messages="errors.priority"
					></v-select>
				</v-col>
				<!-- Project Budget -->
				<v-col cols="12" md="4">
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

			<!-- Select Users and Roles -->
			<v-row>
				<v-col cols="12" md="6">
					<v-select
						v-model="project.members"
						:items="users"
						item-value="id"
						item-title="name"
						label="Select Members"
						multiple
						outlined
						required
						:error-messages="errors.members"
					></v-select>
				</v-col>


				<v-col cols="12" md="6">
					<v-select
						v-model="project.roles"
						:items="roles"
						item-value="id"
						item-title="name"
						label="Select Roles"
						multiple
						outlined
						required
						:error-messages="errors.roles"
					></v-select>
				</v-col>
			</v-row>

			<!-- File Input for Project -->
			<v-row>
				<v-col cols="12" md="6">
					<v-file-input
						v-model="project.attachment"
						label="Project Attachment"
						accept="image/*, .pdf, .docx, .xlsx"
						outlined
						:error-messages="errors.file"
						required
					></v-file-input>
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
import BudgetClient from "../../budget/client";

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
				priority: "-",
				budget_id: null,
				members: [],
				roles: [],
				attachment: null,
			},
			users: [],
			roles: [],
			// budgets to be updated
			budgets: [],
			statusOptions: ["Ongoing", "Completed", "Pending"],
			priorityOptions: ["-","Low", "Medium", "High"],
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
				formattedName: `${budget.name} - RM${budget.total_budget.toLocaleString()}`,
			}));
		},
	},
	mounted() {
		this.initializeDates();
		this.fetchUsersAndRoles();
		this.fetchBudgets();

		if (this.isEdit) {
			const projectId = this.$route.params.id;
			if (projectId) {
				this.fetchProject(projectId);
			}
		}else{
			this.modelLoading = false;
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
		fetchBudgets(){
			ProjectClient.fetchBudgets()
				.then(response => {
					this.budgets = response.data.data
				})
				.catch(error => {
					console.error("Error fetching budgets", error);
				});
		},
		fetchUsersAndRoles() {
			// Fetch users and roles from the backend
			ProjectClient.fetchUsersAndRoles()
				.then(response => {
					this.users = response.data.users;
					this.roles = response.data.projectRoles;
				})
				.catch(error => {
					console.error("Error fetching users and roles", error);
				});
		},
		submitProject() {
			this.isLoading = true;
			this.errors = {};

			const projectData = {
				name: this.project.name,
				description: this.project.description,
				start_date: this.project.start_date,
				end_date: this.project.end_date,
				status: this.project.status,
				priority: this.project.priority,
				budget_id: this.project.budget_id,
				members: this.project.members,
				roles: this.project.roles,
				attachment: this.project.attachment[0],
			};

			ProjectClient.createProject(projectData)
				.then((response) => {
					this.$toast.success("Project created successfully");
					this.$router.push({ name: "project-listings-page" });
				})
				.catch((error) => {
					this.errors = error.response?.data.errors || {};
				})
				.finally(() => {
					this.isLoading = false;
				});
		},
		updateProject() {
			this.isLoading = true;
			this.errors = {};
			ProjectClient.updateProject(this.project)
				.then((response) => {
					this.$toast.success("Project updated successfully");
					this.$router.push({ name: "project-listings-page" });
				})
				.catch((error) => {
					this.errors = error.response?.data.errors || {};
				}).finally(()=>{
					this.isLoading = false;
				});
		},
		fetchProject(id) {
			ProjectClient.fetchProject(id)
				.then(response => {
					const { project, members, roles } = response.data;

					// Populate project fields
					this.project = {
						...project,
						members: members.map(member => member.id),
						roles: members.map(member => member.role_id),
					};

					this.roles = roles;
				})
				.catch(error => {
					console.error("Error fetching project:", error);
				}).finally(() => {
					this.modelLoading = false;
				});
		}
	},
};
</script>
