import axios from 'axios';

const prefix = "/project"

const ProjectClient = {

	getProjectListings(payload) {
		return axios.get( prefix + "/listings", { params: payload} )
	},

	createProject(payload){
		return axios.post( prefix + "/create" , payload)
	},

	fetchProject(id){
		return axios.get( prefix + "/info/" + id )
	},

	updateProject(payload){
		return axios.post( prefix + "/update" , payload)
	}
}

export default ProjectClient;