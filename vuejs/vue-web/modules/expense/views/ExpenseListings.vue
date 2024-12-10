<template>
	<v-container>
		<h2>Expense Listings</h2>
		<v-divider></v-divider>

		<!-- Search Expenses and Create Button -->
		<v-row class="d-flex align-center mb-4">
			<v-col cols="12" md="6">
				<v-text-field
					v-model="searchQuery"
					label="Search Expenses"
					placeholder="Type to search..."
					clearable
					outlined
					dense
					@input="fetchExpenses"
				></v-text-field>
			</v-col>
			<v-col v-if="isAuthorized" cols="12" class="d-flex justify-end">
				<v-btn depressed @click="createExpense">Create Expense</v-btn>
			</v-col>
		</v-row>

		<template v-if="hasData">
			<template v-if="modelLoading">
				<v-skeleton-loader type="article"></v-skeleton-loader>
			</template>
			<template v-else>
				<section>
					<v-list two-line class="px-6 transparent-list">
						<v-divider></v-divider>
						<v-list-item v-for="expense in expenses" :key="expense.id">
							<v-row class="pa-2 align-center">
								<!-- Expense Details -->
								<v-col cols="12" sm="6">
									<v-list-item-title class="font-weight-bold text-wrap">
										{{ expense.name }}
									</v-list-item-title>
									<v-list-item-subtitle style="line-height: unset !important;">
										Amount: {{ formatCurrency(expense.amount) }}
									</v-list-item-subtitle>
									<v-list-item-subtitle style="line-height: unset !important;">
										Date Incurred: {{ expense.date_incurred }}
									</v-list-item-subtitle>
								</v-col>

								<!-- Additional Info -->
								<v-col cols="12" sm="4" class="d-flex align-center">
									<v-chip class="mb-1" outlined small>
										Category: {{ expense.expense_category ? expense.expense_category.name : 'N/A' }}
									</v-chip>
								</v-col>

								<!-- Actions -->
								<v-col
									v-if="isAuthorized" cols="12" sm="2"
									class="text-end"
								>
									<v-list-item-action class="justify-content-md-end">
										<v-menu>
											<template #activator="{ props }">
												<v-btn icon v-bind="props">
													<v-icon>mdi-dots-vertical</v-icon>
												</v-btn>
											</template>
											<v-list>
												<v-list-item @click="editExpense(expense)">
													<v-list-item-title>Edit</v-list-item-title>
												</v-list-item>
												<v-list-item @click="confirmDelete(expense.id)">
													<v-list-item-title>Delete</v-list-item-title>
												</v-list-item>
											</v-list>
										</v-menu>
									</v-list-item-action>
								</v-col>
							</v-row>
						</v-list-item>
						<v-divider></v-divider>
					</v-list>
				</section>
				<!-- Pagination -->
				<v-pagination
					v-model="currentPage"
					:length="paginationLength"
					class="mt-4"
					@input="fetchExpenses"
				></v-pagination>
				<!-- Confirmation Dialog -->
				<v-dialog v-model="deleteDialog" max-width="500px">
					<v-card>
						<v-card-title class="text-h6">Confirm Delete</v-card-title>
						<v-card-text>
							Are you sure you want to delete this expense? This action cannot be undone.
						</v-card-text>
						<v-card-actions>
							<v-spacer></v-spacer>
							<v-btn text @click="deleteDialog = false">Cancel</v-btn>
							<v-btn
								:loading="isLoading" color="red" text
								@click="deleteExpense"
							>
								Delete
							</v-btn>
						</v-card-actions>
					</v-card>
				</v-dialog>
			</template>
		</template>
		<template v-else>
			<!-- Show No Users Image -->
			<v-row class="justify-center">
				<v-col cols="12" class="text-center">
					<img src="/images/no-product-available.png" alt="No expenses available" class="my-4" />
					<p>No expenses available.</p>
				</v-col>
			</v-row>
		</template>
	</v-container>
</template>

<script>
import ExpenseClient from "../client";

export default {
	data() {
		return {
			hasData: true,
			modelLoading: true,
			expenses: [],
			searchQuery: "",
			currentPage: 1,
			paginationLength: 0,
			itemsPerPage: 10,
			deleteDialog: false,
			selectedExpenseId: null,
			isLoading: false,
		};
	},
	computed: {
		isAuthorized() {
			const userRole = localStorage.getItem('userRole');
			return userRole === 'Admin';
		}
	},
	mounted() {
		this.fetchExpenses();
	},
	methods: {
		// Create a new expense
		createExpense() {
			this.$router.push({ name: "expense-create-page" });
		},

		// Edit an existing expense
		editExpense(expense) {
			this.$router.push({ name: "expense-edit-page", params: { id: expense.id } });
		},

		// Confirm the delete action
		confirmDelete(expenseId) {
			this.selectedExpenseId = expenseId;
			this.deleteDialog = true;
		},

		// Delete an expense
		deleteExpense() {
			this.isLoading = true;
			if (this.selectedExpenseId) {
				ExpenseClient.deleteExpense(this.selectedExpenseId)
					.then(() => {
						this.isLoading = false;
						this.expenses = this.expenses.filter(expense => expense.id !== this.selectedExpenseId);
						this.paginationLength -= 1;
						this.$toast.success("Expense deleted successfully.");
						if (this.expenses.length === 0) {
							this.paginationLength = 0;
						}
					})
					.catch((error) => {
						console.error("Error deleting expense:", error);
					})
					.finally(() => {
						this.isLoading = false;
						this.deleteDialog = false;
						this.selectedExpenseId = null;
					});
			}
		},

		// Fetch expenses from the server with search query and pagination
		async fetchExpenses() {
			try {
				this.modelLoading = true;
				this.hasData = true;
				const params = {
					searchQuery: this.searchQuery,
					itemsPerPage: this.itemsPerPage,
					page: this.currentPage,
				};

				const response = await ExpenseClient.getExpenseListings(params);
				this.expenses = response.data.data;
				this.hasData = this.expenses.length > 0;
				this.paginationLength = response.data.last_page;
			} catch (error) {
				console.error("Error fetching expenses:", error);
				this.hasData = false;
			}
			this.modelLoading = false
		},

		// Format currency for the expense
		formatCurrency(value) {
			return new Intl.NumberFormat("en-US", {
				style: "currency",
				currency: "USD",
			}).format(value);
		},
	},
};
</script>
