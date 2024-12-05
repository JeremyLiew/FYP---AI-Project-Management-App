import axios from 'axios';

const prefix = "/user-maintenance"

const UserMaintenanceClient = {
	getAllUsers(){
		return axios.get( prefix + "/users" );
	}
}

export default UserMaintenanceClient;