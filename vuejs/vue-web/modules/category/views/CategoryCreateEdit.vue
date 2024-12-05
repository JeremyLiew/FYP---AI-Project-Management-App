<template>
	<v-container>
	  <v-row>
		<v-col cols="12">
		  <h1 class="text-subtitle-1">{{ isEdit ? "Edit Expense Category" : "Create New Expense Category" }}</h1>
		</v-col>
	  </v-row>
  
	  <v-skeleton-loader v-if="modelLoading" type="article"></v-skeleton-loader>
  
	  <v-form v-else ref="expenseCategoryForm">
		<!-- Category Name -->
		<v-row>
		  <v-col cols="12">
			<v-text-field
			  v-model="expenseCategory.name"
			  label="Category Name *"
			  required
			  :error-messages="errors.name"
			></v-text-field>
		  </v-col>
  
		  <!-- Description -->
		  <v-col cols="12">
			<v-text-field
			  v-model="expenseCategory.description"
			  label="Description"
			  multiline
			  outlined
			  :error-messages="errors.description"
			></v-text-field>
		  </v-col>
		</v-row>
  
		<!-- Submit Button -->
		<v-row>
		  <v-col cols="12" style="text-align: end;">
			<v-btn
			  depressed
			  :loading="isLoading"
			  @click="isEdit ? updateExpenseCategory() : submitExpenseCategory()"
			>
			  {{ isEdit ? "Update Category" : "Create Category" }}
			</v-btn>
		  </v-col>
		</v-row>
	  </v-form>
	</v-container>
  </template>
  
  <script>
  import ExpenseCategoryClient from "../client"; // Assuming ExpenseCategoryClient handles category-related API calls
  
  export default {
	props: {
	  isEdit: {
		type: Boolean,
		default: false,
	  },
	},
	data() {
	  return {
		expenseCategory: {
		  name: "",
		  description: "",
		},
		errors: {},
		isLoading: false,
		modelLoading: true,
	  };
	},
	mounted() {
	  if (this.isEdit) {
		const categoryId = this.$route.params.id;
		if (categoryId) {
		  this.fetchExpenseCategory(categoryId);
		}
	  } else {
		this.modelLoading = false;
	  }
	},
	methods: {
	  submitExpenseCategory() {
		this.isLoading = true;
		this.errors = {};
		ExpenseCategoryClient.createCategory(this.expenseCategory)
		  .then(() => {
			this.$toast.success("Expense category created successfully");
			this.$router.push({ name: "expense-category-listings-page" });
		  })
		  .catch((error) => {
			this.errors = error.response?.data.errors || {};
		  })
		  .finally(() => {
			this.isLoading = false;
		  });
	  },
	  updateExpenseCategory() {
		this.isLoading = true;
		this.errors = {};
		ExpenseCategoryClient.updateCategory(this.expenseCategory)
		  .then(() => {
			this.$toast.success("Expense category updated successfully");
			this.$router.push({ name: "expense-category-listings-page" });
		  })
		  .catch((error) => {
			this.errors = error.response?.data.errors || {};
		  })
		  .finally(() => {
			this.isLoading = false;
		  });
	  },
	  fetchExpenseCategory(id) {
		ExpenseCategoryClient.fetchCategory(id)
		  .then((response) => {
			this.expenseCategory = {
			  id: response.data.expenseCategory.id,
			  name: response.data.expenseCategory.name,
			  description: response.data.expenseCategory.description,
			};
		  })
		  .catch((error) => {
			console.error("Error fetching expense category:", error);
		  })
		  .finally(() => {
			this.modelLoading = false;
		  });
	  },
	},
  };
  </script>
  