<template>
	<v-container>
		<template v-if="isAuthorized">
			<v-row>
				<v-col cols="12">
					<h1 class="text-subtitle-1">{{ isEdit ? "Edit Expense" : "Create New Expense" }}</h1>
				</v-col>
			</v-row>
			<v-skeleton-loader v-if="modelLoading" type="article"></v-skeleton-loader>
			<v-form v-else ref="expenseForm">
				<!-- Expense Name -->
				<v-row>
					<v-col cols="12">
						<v-text-field
							v-model="expense.name"
							label="Expense Name *"
							required
							:error-messages="errors.name"
						></v-text-field>
					</v-col>
					<!-- Amount -->
					<v-col cols="12" md="6">
						<v-text-field
							v-model="expense.amount"
							label="Amount *"
							type="number"
							required
							outlined
							:error-messages="errors.amount"
						></v-text-field>
					</v-col>
					<!-- Description -->
					<v-col cols="12" md="6">
						<v-text-field
							v-model="expense.description"
							label="Description"
							multiline
							outlined
							:error-messages="errors.description"
						></v-text-field>
					</v-col>
					<!-- Expense Date -->
					<v-col cols="12" md="6">
						<v-text-field
							v-model="expense.date_incurred"
							label="Date"
							type="date"
							outlined
							:error-messages="errors.date_incurred"
						></v-text-field>
					</v-col>
					<!-- Project Selection -->
					<v-col cols="12" md="6">
						<v-select
						v-model="expense.project_id"
						:items="projects"
						item-title="name"
						item-value="id"
						label="Select Project *"
						required
						outlined
						@change="filterTasksByProject" 
						:error-messages="errors.project_id"
						></v-select>
					</v-col>
					<!-- Expense Category Selection -->
					<v-col cols="12" md="6">
						<v-select
							v-model="expense.expense_category_id"
							:items="expenseCategories"
							item-title="name"
							item-value="id"
							label="Select Expense Category *"
							required
							outlined
							:error-messages="errors.expense_category_id"
						></v-select>
					</v-col>
					<!-- Task ID Selection -->
					<v-col cols="12" md="6">
						<v-select
							v-model="expense.task_id"
							:items="tasks"
							item-title="name"
							item-value="id"
							label="Select Task"
							outlined
							:error-messages="errors.task_id"
						></v-select>
					</v-col>
					<!-- Budget ID Selection -->
					<v-col cols="12" md="6">
						<v-select
							v-model="expense.budget_id"
							:items="budgets"
							item-title="name"
							item-value="id"
							label="Select Budget"
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
							@click="isEdit ? updateExpense() : submitExpense()"
						>
							{{ isEdit ? "Update Expense" : "Create Expense" }}
						</v-btn>
					</v-col>
				</v-row>
			</v-form>
		</template>
		<template v-else>
			<p>You do not have permission to view this page.</p>
		</template>
	</v-container>
</template>

<script>
import ExpenseClient from "../client"; // Assuming ExpenseClient handles expense-related API calls


export default {
	props: {
		isEdit: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			expense: {
				name: "",
				amount: 0,
				description: "",
				date_incurred: "",
				project_id: null,
				expense_category_id: null,
				task_id: null,
				budget_id: null,
			},
			errors: {},
			isLoading: false,
			modelLoading: true,
			projects: [], // List of projects
			expenseCategories: [], // List of expense categories
			tasks: [], // List of tasks
			taskByProject:[],
			budgets: [],
		};
	},
	computed: {
		isAuthorized() {
			const userRole = localStorage.getItem('userRole');
			return userRole === 'Admin';
		}
	},
	mounted() {
		this.fetchProjects();
		this.fetchExpenseCategories();
		this.fetchTasks();
		this.fetchBudgets();
		console.log('Selected Project ID:', this.expense.project_id);  // Check if project ID is selected
		console.log('All Tasks:', this.budgets); // Check all tasks available

		if (this.isEdit) {
			const expenseId = this.$route.params.id;
			if (expenseId) {
				this.fetchExpense(expenseId);
			}
		} else {
			this.modelLoading = false;
		}
	},
	methods: {
		submitExpense() {
			this.isLoading = true;
			this.errors = {};
			ExpenseClient.createExpense(this.expense)
				.then(() => {
					this.$toast.success("Expense created successfully");
					this.$router.push({ name: "expense-listings-page" });
				})
				.catch((error) => {
					this.errors = error.response?.data.errors || {};
				})
				.finally(() => {
					this.isLoading = false;
				});
		},
		updateExpense() {
			this.isLoading = true;
			this.errors = {};
			ExpenseClient.updateExpense(this.expense)
				.then(() => {
					this.$toast.success("Expense updated successfully");
					this.$router.push({ name: "expense-listings-page" });
				})
				.catch((error) => {
					this.errors = error.response?.data.errors || {};
				})
				.finally(() => {
					this.isLoading = false;
				});
		},
		fetchExpense(id) {
			ExpenseClient.fetchExpense(id)
				.then((response) => {
					this.expense = {
						id: response.data.expense.id,
						name: response.data.expense.name,
						amount: response.data.expense.amount,
						description: response.data.expense.description,
						date_incurred: response.data.expense.date_incurred,
						expense_category_id: response.data.expense.expense_category_id,
						project_id: response.data.expense.project_id,
						task_id: response.data.expense.task_id,
						budget_id: response.data.expense.budget_id,
					};
				})
				.catch((error) => {
					console.error("Error fetching expense:", error);
				})
				.finally(() => {
					this.modelLoading = false;
				});
		},
		fetchProjects() {
			ExpenseClient.fetchProjects()
				.then(response => {
					this.projects = response.data.projects;
				})
				.catch(error => {
					console.error("Error fetching projects:", error);
				});
		},
		fetchExpenseCategories() {
			ExpenseClient.fetchExpenseCategories()
				.then((response) => {
					this.expenseCategories = response.data.expenseCategories;
				})
				.catch((error) => {
					console.error("Error fetching expense categories:", error);
				});
		},
		fetchTasks() {
			ExpenseClient.fetchTasks()
				.then((response) => {
					this.tasks = response.data.tasks;
					this.taskByProject = response.data.tasks; 
				})
				.catch((error) => {
					console.error("Error fetching tasks:", error);
				});
		},
		fetchBudgets() {
			ExpenseClient.fetchBudgets()
				.then((response) => {
					this.budgets = response.data.budgets;
				})
				.catch((error) => {
					console.error("Error fetching budgets:", error);
				});
		},
		filterTasksByProject() {
		console.log('Selected Project ID:', this.expense.project_id);  // Check if project ID is selected
		console.log('All Tasks:', this.allTasks); // Check all tasks available
		
		this.tasks = this.allTasks.filter(task => task.project_id === this.expense.project_id);
		console.log('Filtered Tasks:', this.tasks);  // Check filtered tasks
		},
	},
};
</script>
