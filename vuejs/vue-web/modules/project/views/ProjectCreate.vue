<template>
	<v-container>
		<v-row>
			<v-col cols="12">
				<h1 class="text-subtitle-1">Create New Project</h1>
			</v-col>
		</v-row>
  
		<v-form v-model="formValid">
			<!-- Project Name -->
			<v-row>
				<v-col cols="12" md="6">
					<v-text-field
						v-model="project.name"
						label="Project Name"
						:rules="[nameRules]"
						required
					></v-text-field>
				</v-col>
			</v-row>
  
			<!-- Project Description -->
			<v-row>
				<v-col cols="12" md="6">
					<v-textarea
						v-model="project.description"
						label="Project Description"
						:rules="[descriptionRules]"
						outlined
					></v-textarea>
				</v-col>
			</v-row>
  
			<!-- Project Start Date -->
			<v-row>
				<v-col cols="12" md="6">
					<v-dialog
						v-model="startDateDialog"
						persistent
						max-width="290px"
					>
						<template #activator="{ on, attrs }">
							<v-text-field
								v-model="project.start_date"
								label="Start Date"
								readonly
								:rules="[dateRules]"
								required
								v-bind="attrs"
								outlined
								v-on="on"
							></v-text-field>
						</template>
						<v-date-picker v-model="project.start_date" @input="startDateDialog = false"></v-date-picker>
					</v-dialog>
				</v-col>
			</v-row>
  
			<!-- Project End Date -->
			<v-row>
				<v-col cols="12" md="6">
					<v-dialog
						v-model="endDateDialog"
						persistent
						max-width="290px"
					>
						<template #activator="{ on, attrs }">
							<v-text-field
								v-model="project.end_date"
								label="End Date"
								readonly
								:rules="[dateRules]"
								required
								v-bind="attrs"
								outlined
								v-on="on"
							></v-text-field>
						</template>
						<v-date-picker v-model="project.end_date" @input="endDateDialog = false"></v-date-picker>
					</v-dialog>
				</v-col>
			</v-row>
  
			<!-- Project Status -->
			<v-row>
				<v-col cols="12" md="6">
					<v-select
						v-model="project.status"
						:items="statusOptions"
						label="Status"
						:rules="[statusRules]"
						required
						outlined
					></v-select>
				</v-col>
			</v-row>
  
			<!-- Project Budget -->
			<v-row>
				<v-col cols="12" md="6">
					<v-select
						v-model="project.budget_id"
						:items="budgets"
						item-text="name"
						item-value="id"
						label="Budget"
						:rules="[budgetRules]"
						required
						outlined
					></v-select>
				</v-col>
			</v-row>
  
			<!-- Submit Button -->
			<v-row>
				<v-col cols="12">
					<v-btn 
						:disabled="!formValid" 
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
export default {
	data() {
		return {
			project: {
				name: '',
				description: '',
				start_date: '',
				end_date: '',
				status: '',
				budget_id: null,
			},
			budgets: [], // To store fetched budgets
			statusOptions: ['Ongoing', 'Completed', 'Pending'], // Options for project status
			formValid: false, // Form validation state
			startDateDialog: false, // Dialog for start date picker
			endDateDialog: false, // Dialog for end date picker
			nameRules: [(v) => !!v || 'Project name is required'],
			descriptionRules: [(v) => v === '' || !!v || 'Description is optional'],
			dateRules: [(v) => !!v || 'Date is required'],
			statusRules: [(v) => !!v || 'Status is required'],
			budgetRules: [(v) => !!v || 'Budget is required'],
		};
	},
	mounted() {
		this.fetchBudgets();
	},
	methods: {
		// Fetch available budgets from the database
		async fetchBudgets() {
			try {
				const response = await this.$axios.get('/api/budgets'); // Adjust the API endpoint
				this.budgets = response.data;
			} catch (error) {
				console.error('Error fetching budgets:', error);
			}
		},
		// Submit project creation form
		async submitProject() {
			try {
				const response = await this.$axios.post('/api/projects', this.project); // Adjust the API endpoint
				this.$router.push({ name: 'ProjectList' }); // Navigate to project list after success
			} catch (error) {
				console.error('Error creating project:', error);
			}
		},
	},
};
</script>
  
  <style scoped>
  /* You can add custom styles for the project create page here */
  </style>
  