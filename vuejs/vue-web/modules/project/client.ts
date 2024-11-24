import axios from 'axios';

const prefix = "/project"

const ProjectClient = {

	getProjectListings(payload) {
		return axios.get( prefix + "/listings", { params: payload} )
	},

	createProject(payload){
		return axios.post( prefix + "/create" , payload)
	}
}

export default ProjectClient;