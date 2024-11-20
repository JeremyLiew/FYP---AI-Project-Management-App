import axios from 'axios';

const prefix = "/auth"

const AuthClient = {

	forgotPassword(payload){
		return axios.post( prefix + "/forgot-password", payload)
	},
	resetPassword(payload){
		return axios.post( prefix + "/reset-password", payload)
	}

}

export default AuthClient;