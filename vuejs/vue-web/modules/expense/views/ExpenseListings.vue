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
        <v-col cols="12" class="d-flex justify-end">
          <v-btn depressed @click="createExpense">Create Expense</v-btn>
        </v-col>
      </v-row>
  
      <section>
        <v-list two-line>
          <v-list-item v-for="expense in expenses" :key="expense.id">
            <v-list-item-content>
              <v-list-item-title>{{ expense.name }}</v-list-item-title>
              <v-list-item-subtitle>
                Amount: {{ formatCurrency(expense.amount) }}
              </v-list-item-subtitle>
              <v-list-item-subtitle>
                Date Incurred: {{ expense.date_incurred }}
              </v-list-item-subtitle>
              <v-list-item-subtitle>
                Category: {{ expense.category_name }}
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
                  <v-list-item @click="editExpense(expense)">
                    <v-list-item-title>Edit</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="confirmDelete(expense.id)">
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
        @input="fetchExpenses"
        class="mt-4"
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
            <v-btn :loading="isLoading" color="red" text @click="deleteExpense">Delete</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </template>
  
  <script>
  import ExpenseClient from "../client";
  
  export default {
    data() {
      return {
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
          const params = {
            searchQuery: this.searchQuery,
            itemsPerPage: this.itemsPerPage,
            page: this.currentPage,
          };
  
          const response = await ExpenseClient.getExpenseListings(params);
          this.expenses = response.data.data;
          this.paginationLength = response.data.last_page;
        } catch (error) {
          console.error("Error fetching expenses:", error);
        }
      },
  
      // Format currency for the expense
      formatCurrency(value) {
        return new Intl.NumberFormat("en-US", {
          style: "currency",
          currency: "USD",
        }).format(value);
      },
    },
    mounted() {
      this.fetchExpenses();
    },
  };
  </script>
  
  <style scoped>
  /* Add any specific styles for the Expense module */
  </style>
  