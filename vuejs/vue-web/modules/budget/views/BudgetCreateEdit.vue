<template>
	<v-container>
		<template v-if="isAuthorized">
			<v-row>
				<v-col cols="12">
					<h1 class="text-subtitle-1">{{ isEdit ? "Edit Budget" : "Create New Budget" }}</h1>
				</v-col>
			</v-row>
			<v-skeleton-loader v-if="modelLoading" type="article"></v-skeleton-loader>
			<v-form v-else ref="budgetForm">
				<!-- Budget Name -->
				<v-row>
					<v-col cols="12">
						<v-text-field
							v-model="budget.name"
							label="Budget Name *"
							required
							:error-messages="errors.name"
						></v-text-field>
					</v-col>
					<!-- Total Budget -->
					<v-col cols="12" md="6">
						<v-text-field
							v-model="budget.total_budget"
							label="Total Budget *"
							type="number"
							required
							outlined
							:error-messages="errors.total_budget"
						></v-text-field>
					</v-col>
				</v-row>
				<!-- Submit Button -->
				<v-row>
					<v-col cols="12" style="text-align: end;">
						<v-btn
							depressed
							:loading="isLoading"
							@click="isEdit ? updateBudget() : submitBudget()"
						>
							{{ isEdit ? "Update Budget" : "Create Budget" }}
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
import BudgetClient from "../client";

export default {
	props: {
		isEdit: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			budget: {
				name: "",
				total_budget: 0,
				remaining_amount : 0,
			},
			errors: {},
			isLoading: false,
			modelLoading: true,
		};
	},
	computed: {
		isAuthorized() {
			const userRole = localStorage.getItem('userRole');
			return userRole === 'Admin';
		}
	},
	mounted() {
		if (this.isEdit) {
			const budgetId = this.$route.params.id;
			if (budgetId) {
				this.fetchBudget(budgetId);
			}
		} else {
			this.modelLoading = false;
		}
	},
	methods: {
		submitBudget() {
			this.isLoading = true;
			this.errors = {};
			BudgetClient.createBudget(this.budget)
				.then(() => {
					this.$toast.success("Budget created successfully");
					this.$router.push({ name: "budget-listings-page" });
				})
				.catch((error) => {
					this.errors = error.response?.data.errors || {};
				})
				.finally(() => {
					this.isLoading = false;
				});
		},
		updateBudget() {
			this.isLoading = true;
			this.errors = {};
			this.remaining_amount = this.budget.total_budget;

			BudgetClient.updateBudget(this.budget)
				.then(() => {
					this.$toast.success("Budget updated successfully");
					this.$router.push({ name: "budget-listings-page" });
				})
				.catch((error) => {
					this.errors = error.response?.data.errors || {};
				})
				.finally(() => {
					this.isLoading = false;
				});
		},
		fetchBudget(id) {
			this.modelLoading = true;
			BudgetClient.fetchBudget(id)
				.then((response) => {
				// Assign the API response to the budget object
					this.budget = {
						id: response.data.budget.id, // Add the id here
						name: response.data.budget.name,
						total_budget: response.data.budget.total_budget,
					};
				})
				.catch((error) => {
					console.error("Error fetching budget:", error);
					this.$toast.error("Failed to load budget data.");
				})
				.finally(() => {
					this.modelLoading = false;
				});
		}
	},
};
</script>
