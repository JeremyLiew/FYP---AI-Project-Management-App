import axios from "axios";

const prefix = "/budget";

const BudgetClient = {
  // Fetch list of budgets
  getBudgetListings(payload) {
    return axios.get(prefix + "/listings", { params: payload });
  },

  // Create a new budget
  createBudget(payload) {
    return axios.post(prefix + "/create", payload);
  },

  // Delete a budget
  deleteBudget(budgetId) {
    return axios.post(`${prefix}/delete/${budgetId}`);
  },

  // Update a budget
  updateBudget(payload) {
    return axios.post(`${prefix}/update`, payload);
  },

  fetchBudget(id){
		return axios.get( prefix + "/info/" + id )
	},

};

export default BudgetClient;
