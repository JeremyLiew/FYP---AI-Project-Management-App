import axios from "axios";

const prefix = "/expense";
const pre = "/categories";

const ExpenseClient = {
  // Fetch list of expenses
  getExpenseListings(payload: any) {
    return axios.get(`${prefix}/listings`, { params: payload });
  },

  // Create a new expense
  createExpense(payload: any) {
    return axios.post(`${prefix}/create`, payload);
  },

  // Delete an expense
  deleteExpense(expenseId: number | string) {
    return axios.post(`${prefix}/delete/${expenseId}`);
  },

  updateExpense(payload) {
    return axios.post(`${prefix}/update`, payload);
  },

  // Fetch a specific expense by ID
  fetchExpense(expenseId: number | string) {
    return axios.get(`${prefix}/info/${expenseId}`);
  },

  // Fetch list of projects
  fetchProjects() {
    return axios.get(prefix + "/projects"); 
  },

  // Fetch list of tasks
  fetchTasks() {
    return axios.get(prefix + "/tasks"); 
  },

  // Fetch list of expense categories
  fetchExpenseCategories() {
    return axios.get(prefix + "/expense-categories"); 
  },

  // Fetch list of tasks
  fetchBudgets() {
    return axios.get(prefix + "/budgets"); 
  },

  // Fetch list of expense categories
  fetchCategories(Id: number | string) {
    return axios.get(`${prefix}/${pre}/info/${Id}`);
  },
};

export default ExpenseClient;
