import axios from 'axios';

const prefix = "/weather"

const WeatherClient = {

	getProjectsAndTasks(payload) {
		return axios.get( prefix + "/listings", { params: payload })
	},

	getExpenseCategoryData(payload) {
		return axios.get( prefix + "/expense", { params: payload })
	},


}

export default WeatherClient;