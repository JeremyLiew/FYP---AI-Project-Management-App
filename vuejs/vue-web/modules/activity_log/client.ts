import axios from 'axios';

const prefix = "/activity-log"

const ActivityLogClient = {

	getActivityLogs(params) {
		return axios.get( prefix + '/listings', { params });
	},

	getAllUsers(){
		return axios.get( prefix + "/users" );
	}
}

export default ActivityLogClient;