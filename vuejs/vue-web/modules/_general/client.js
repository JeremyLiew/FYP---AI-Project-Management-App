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
	},

	fetchNotifications(){
		return axios.get( prefix + "/notifications" )
	},

	markAsRead($id){
		return axios.post( prefix + "/notifications/" + $id + "/read")
	},

}

export default GeneralClient;