<template>
  <v-container>
    <h2>Expense Categories</h2>
    <v-divider></v-divider>

    <!-- Search Categories and Create Button -->
    <v-row class="d-flex align-center mb-4">
      <v-col cols="12" md="6">
        <v-text-field
          v-model="searchQuery"
          label="Search Categories"
          placeholder="Type to search..."
          clearable
          outlined
          dense
          @input="fetchCategories"
        ></v-text-field>
      </v-col>
      <v-col cols="12" class="d-flex justify-end">
        <v-btn depressed @click="createCategory">Create Category</v-btn>
      </v-col>
    </v-row>

    <section>
      <v-list two-line>
        <v-list-item v-for="category in categories" :key="category.id">
          <v-list-item-content>
            <v-list-item-title>{{ category.name }}</v-list-item-title>
            <v-list-item-subtitle>
              Description: {{ category.description || 'No description provided' }}
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
                <v-list-item @click="editCategory(category)">
                  <v-list-item-title>Edit</v-list-item-title>
                </v-list-item>
                <v-list-item @click="confirmDelete(category.id)">
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
      @input="fetchCategories"
      class="mt-4"
    ></v-pagination>

    <!-- Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="500px">
      <v-card>
        <v-card-title class="text-h6">Confirm Delete</v-card-title>
        <v-card-text>
          Are you sure you want to delete this category? This action cannot be undone.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="deleteDialog = false">Cancel</v-btn>
          <v-btn :loading="isLoading" color="red" text @click="deleteCategory">Delete</v-btn>
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
      categories: [],
      searchQuery: "",
      currentPage: 1,
      paginationLength: 0,
      itemsPerPage: 10,
      deleteDialog: false,
      selectedCategoryId: null,
      isLoading: false,
    };
  },
  methods: {
    // Create a new category
    createCategory() {
      this.$router.push({ name: "expense-category-create-page" });
    },

    // Edit an existing category
    editCategory(category) {
      this.$router.push({ name: "expense-category-edit-page", params: { id: category.id } });
    },

    // Confirm the delete action
    confirmDelete(categoryId) {
      this.selectedCategoryId = categoryId;
      this.deleteDialog = true;
    },

    // Delete a category
    deleteCategory() {
      this.isLoading = true;
      if (this.selectedCategoryId) {
        ExpenseClient.deleteCategory(this.selectedCategoryId)
          .then(() => {
            this.isLoading = false;
            this.categories = this.categories.filter(category => category.id !== this.selectedCategoryId);
            this.paginationLength -= 1;
            this.$toast.success("Category deleted successfully.");
            if (this.categories.length === 0) {
              this.paginationLength = 0;
            }
          })
          .catch((error) => {
            console.error("Error deleting category:", error);
          })
          .finally(() => {
            this.isLoading = false;
            this.deleteDialog = false;
            this.selectedCategoryId = null;
          });
      }
    },

    // Fetch categories from the server with search query and pagination
    async fetchCategories() {
      try {
        const params = {
          searchQuery: this.searchQuery,
          itemsPerPage: this.itemsPerPage,
          page: this.currentPage,
        };

        const response = await ExpenseClient.getCategoryListings(params);
        this.categories = response.data.data;
        this.paginationLength = response.data.last_page;
      } catch (error) {
        console.error("Error fetching categories:", error);
      }
    },
  },
  mounted() {
    this.fetchCategories();
  },
};
</script>

