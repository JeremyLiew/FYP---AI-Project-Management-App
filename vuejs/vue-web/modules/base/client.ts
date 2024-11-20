import axios from 'axios';

const prefix = "/base"

const BaseClient = {

	getHomePage(payload) {
		return axios.get( prefix + "/home", { params: payload} )
	},

	submitContactUs(payload){
		return axios.post( prefix + "/contact-us", payload)
	}

}

export default BaseClient;