import axios from "axios";

const prefix = "/categories";

const ExpenseClient = {

  // Fetch list of expense categories
  getCategoryListings(payload: any) {
    return axios.get(`${prefix}/listings`, { params: payload });
  },

  // Create a new expense category
  createCategory(payload: any) {
    return axios.post(`${prefix}/create`, payload);
  },

  // Delete an expense category
  deleteCategory(expenseCategoryId: number | string) {
    return axios.post(`${prefix}/delete/${expenseCategoryId}`);
  },

  // Update an expense category
  updateCategory(expenseCategoryId: number | string, payload: any) {
    return axios.post(`${prefix}/update/${expenseCategoryId}`, payload);
  },

  // Fetch list of expense categories
  fetchCategories(Id: number | string) {
    return axios.get(`${prefix}/info/${Id}`);
  },
};

export default ExpenseClient;
