import axios from 'axios';

const prefix = "/base"

const BaseClient = {

	getHomePage(payload) {
		return axios.get( prefix + "/home", { params: payload} )
	},

	submitContactUs(payload){
		return axios.post( prefix + "/contact-us", payload)
	},

	getGptMessage(payload) {
		return axios.get( prefix + "/message", { params: payload} )
	},

	getFeedbacks(){
		return axios.get ( prefix + "/feedbacks" )
	},

	generateSummaryFeedback() {
		return axios.post( prefix + "/generateSummaryFeedback");
	},

}

export default BaseClient;