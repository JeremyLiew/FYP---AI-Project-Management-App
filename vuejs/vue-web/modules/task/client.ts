import axios from 'axios';

const prefix = "/task"

const TaskClient = {
	getTasksByProject(payload){
		return axios.get( prefix + "/listings" , { params: payload })
	},

	createTask(payload){
		return axios.post( prefix + "/create" , payload)
	},

	fetchTask(id){
		return axios.get( prefix + "/info/" + id )
	},

	updateTask(payload){
		return axios.post( prefix + "/update" , payload)
	},

	deleteTask(id) {
		return axios.post( prefix + "/delete/" + id);
	},

	fetchMembers(projectId){
		return axios.get( prefix + "/" + projectId + "/members")
	}
}

export default TaskClient;