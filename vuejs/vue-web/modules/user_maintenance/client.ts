import axios from 'axios';

const prefix = "/user-maintenance"

const UserMaintenanceClient = {
	getAllUsers(payload){
		return axios.get( prefix + "/users" , { params: payload });
	},
	updateUser(user) {
		return axios.post( prefix + "/update/" + user.id, user);
	},
	deleteUser(userId) {
		return axios.post( prefix + "/delete/" + userId);
	},
}

export default UserMaintenanceClient;