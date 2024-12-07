import axios from 'axios';

const prefix = "/report"

const ReportClient = {

	getProjectsAndTasks(payload) {
		return axios.get( prefix + "/listings", { params: payload })
	},

	getExpenseCategoryData(payload) {
		return axios.get( prefix + "/expense", { params: payload })
	},

	getPerformanceData(payload) {
		return axios.get( prefix + "/performance", { params: payload })
	},

	fetchAiFeedback(payload) {
		return axios.get( prefix + "/feedback", { params: payload })
	},

	fetchProjectExpenses(id){
		return axios.get( prefix + "/project-expense/" + id )
	},

	fetchProjectTasks(id){
		return axios.get( prefix + "/project-task/" + id )
	},

	fetchPrasks(id){
		return axios.get( prefix + "/project-task/" + id )
	},

}

export default ReportClient;