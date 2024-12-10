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

	fetchProfile(){
		return axios.get ( prefix + "/profile")
	},

	uploadProfilePicture(formData) {
		return axios.post(prefix + "/user/profile-picture", formData, {
			headers: {
				'Content-Type': 'multipart/form-data',
			},
		});
	},

	updateUserName(newName) {
		return axios.post( prefix + '/user/update-user-name', { name: newName });
	},

	fetchUserSettings(){
		return axios.get( '/settings' )
	},

	updateSetting(payload){
		return axios.post( '/settings/update' , payload)
	}

}

export default GeneralClient;