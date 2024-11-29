import axios from 'axios';

const prefix = "/general"

const GeneralClient = {

	submitContactUs(payload){
		return axios.post( prefix + "/contact-us", payload)
	},

	getOrderHistory(payload){
		return axios.get( prefix + "/order-history" , { params:payload } )
	},

	fetchNotificationCount(){
		return axios.get( prefix + "/notifications/unread-count" )
	}

}

export default GeneralClient;