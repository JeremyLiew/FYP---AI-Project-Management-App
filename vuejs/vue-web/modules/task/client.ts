import axios from 'axios';

const prefix = "/task"

const TaskClient = {
	getTasksByProject(payload){
		return axios.get( prefix + "/listings" , { params: payload })
	},

	deleteTask(id) {
		return axios.post( prefix + "/delete/" + id);
	},
}

export default TaskClient;