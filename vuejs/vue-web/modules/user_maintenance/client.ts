import axios from 'axios';

const prefix = "/user-maintenance"

const UserMaintenanceClient = {
	getAllUsers(payload){
		return axios.get( prefix + "/users" , { params: payload });
	},
	updateUser(user) {
		return axios.post( prefix + "/update", user);
	},
	deleteUser(userId) {
		return axios.post( prefix + "/delete" + userId);
	},
	getApplicationRoles() {
		return axios.get( prefix + "/application-roles");
	},
}

export default UserMaintenanceClient;