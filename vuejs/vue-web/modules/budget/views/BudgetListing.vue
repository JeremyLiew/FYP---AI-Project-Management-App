<template>
	<v-container>
	  <h2>Budget Listings</h2>
	  <v-divider></v-divider>
  
	  <!-- Search Budget and Create Button -->
	  <v-row class="d-flex align-center mb-4">
		<v-col cols="12" md="6">
		  <v-text-field
			v-model="searchQuery"
			label="Search Budgets"
			placeholder="Type to search..."
			clearable
			outlined
			dense
			@input="fetchBudgets"
		  ></v-text-field>
		</v-col>
		<v-col cols="12" class="d-flex justify-end">
		  <v-btn depressed @click="createBudget">Create Budget</v-btn>
		</v-col>
	  </v-row>
  
	  <section>
		<v-list two-line>
		  <v-list-item v-for="budget in budgets" :key="budget.id">
			<v-list-item-content>
			  <v-list-item-title>{{ budget.name }}</v-list-item-title>
			  <v-list-item-subtitle>
				Total Budget: {{ formatCurrency(budget.total_budget) }}
			  </v-list-item-subtitle>
			</v-list-item-content>
  
			<!-- Actions for Edit and Delete -->
			<v-list-item-action>
			  <v-menu>
				<template #activator="{ props }">
				  <v-btn icon v-bind="props">
					<v-icon>mdi-dots-vertical</v-icon>
				  </v-btn>
				</template>
				<v-list>
				  <v-list-item @click="editBudget(budget)">
					<v-list-item-title>Edit</v-list-item-title>
				  </v-list-item>
				  <v-list-item @click="confirmDelete(budget.id)">
					<v-list-item-title>Delete</v-list-item-title>
				  </v-list-item>
				</v-list>
			  </v-menu>
			</v-list-item-action>
		  </v-list-item>
		</v-list>
	  </section>
  
	  <!-- Pagination -->
	  <v-pagination
		v-model="currentPage"
		:length="paginationLength"
		@input="fetchBudgets"
		class="mt-4"
	  ></v-pagination>
  
	  <!-- Confirmation Dialog -->
	  <v-dialog v-model="deleteDialog" max-width="500px">
		<v-card>
		  <v-card-title class="text-h6">Confirm Delete</v-card-title>
		  <v-card-text>
			Are you sure you want to delete this budget? This action cannot be undone.
		  </v-card-text>
		  <v-card-actions>
			<v-spacer></v-spacer>
			<v-btn text @click="deleteDialog = false">Cancel</v-btn>
			<v-btn :loading="isLoading" color="red" text @click="deleteBudget">Delete</v-btn>
		  </v-card-actions>
		</v-card>
	  </v-dialog>
	</v-container>
  </template>
  
  <script>
  import BudgetClient from "../client";
  
  export default {
	data() {
	  return {
		budgets: [],
		searchQuery: "",
		currentPage: 1,
		paginationLength: 0,
		itemsPerPage: 10,
		deleteDialog: false,
		selectedBudgetId: null,
		isLoading: false,
	  };
	},
	methods: {
	  // Create a new budget
	  createBudget() {
		this.$router.push({ name: "budget-create-page" });
	  },
  
	  // Edit an existing budget
	  editBudget(budget) {
		this.$router.push({ name: "budget-edit-page", params: { id: budget.id } });
	  },
  
	  // Confirm the delete action
	  confirmDelete(budgetId) {
		this.selectedBudgetId = budgetId;
		this.deleteDialog = true;
	  },
  
	  // Delete a budget
	  deleteBudget() {
		this.isLoading = true;
		if (this.selectedBudgetId) {
		  BudgetClient.deleteBudget(this.selectedBudgetId)
			.then(() => {
			  this.isLoading = false;
			  this.budgets = this.budgets.filter(budget => budget.id !== this.selectedBudgetId);
			  this.paginationLength -= 1;
			  this.$toast.success("Budget deleted successfully.");
			  if (this.budgets.length === 0) {
				this.paginationLength = 0;
			  }
			})
			.catch((error) => {
			  console.error("Error deleting budget:", error);
			})
			.finally(() => {
			  this.isLoading = false;
			  this.deleteDialog = false;
			  this.selectedBudgetId = null;
			});
		}
	  },

	  // Fetch budgets from the server with search query and pagination
	  async fetchBudgets() {
		try {
		  const params = {
			searchQuery: this.searchQuery,
			itemsPerPage: this.itemsPerPage,
			page: this.currentPage,
		  };
  
		  const response = await BudgetClient.getBudgetListings(params);
		  this.budgets = response.data.data;
		  this.paginationLength = response.data.last_page;
		} catch (error) {
		  console.error("Error fetching budgets:", error);
		}
	  },
  
	  // Format currency for the budget
	  formatCurrency(value) {
		return new Intl.NumberFormat("en-US", {
		  style: "currency",
		  currency: "USD",
		}).format(value);
	  },
	},
	mounted() {
	  this.fetchBudgets();
	},
  };
  </script>
  
  <style scoped>
  /* Add any specific styles for the Budget module */
  </style>
  