import axios from 'axios';

const prefix = "/user-maintenance"

const UserMaintenanceClient = {
	getAllUsers(payload){
		return axios.get( prefix + "/users" , { params: payload });
	}
}

export default UserMaintenanceClient;