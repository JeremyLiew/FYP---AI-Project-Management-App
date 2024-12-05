import axios from 'axios';

const prefix = "/project"

const ProjectClient = {

	getProjectListings(payload) {
		return axios.get( prefix + "/listings", { params: payload })
	},

	createProject(payload){
		return axios.post( prefix + "/create" , payload)
	},

	fetchProject(id){
		return axios.get( prefix + "/info/" + id )
	},

	updateProject(payload){
		return axios.post( prefix + "/update" , payload)
	},

	deleteProject(id) {
		return axios.post( prefix + "/delete/" + id);
	},

	fetchUsersAndRoles(){
		return axios.get( prefix + "/users-and-roles");
	},

}

export default ProjectClient;