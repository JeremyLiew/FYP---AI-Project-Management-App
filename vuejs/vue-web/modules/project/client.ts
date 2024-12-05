import axios from 'axios';

const prefix = "/project"

const ProjectClient = {

	getProjectListings(payload) {
		return axios.get( prefix + "/listings", { params: payload })
	},

	createProject(formData){
		return axios.post(prefix + "/create", formData, {
			headers: {
			  "Content-Type": "multipart/form-data" // Ensure the request is sent as multipart/form-data
			}
		  });
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

	fetchAttachment(id){
		return axios.get( prefix + "/file/" + id )
	},

	downloadAttachment(id){
		return axios.get( prefix + "/download/" + id )
	},

}

export default ProjectClient;