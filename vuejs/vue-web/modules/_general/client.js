import axios from 'axios';

const prefix = "/general"

const GeneralClient = {

	submitContactUs(payload){
		return axios.post( prefix + "/contact-us", payload)
	},

	getOrderHistory(payload){
		return axios.get( prefix + "/order-history" , { params:payload } )
	}

}

export default GeneralClient;