import axios from 'axios';

const prefix = "/task"

const TaskClient = {
	getTasksByProject(payload){
		return axios.get( prefix + "/listings" , { params: payload })
	}
}

export default TaskClient;