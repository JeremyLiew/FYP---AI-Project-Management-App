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
  updateCategory(payload: any) {
    return axios.post(`${prefix}/update`, payload);
  },

  // Fetch list of expense categories
  fetchCategory(Id: number | string) {
    return axios.get(`${prefix}/info/${Id}`);
  },
};

export default ExpenseClient;
