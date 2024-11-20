import axios from 'axios';

const prefix = "/products"

const ProductClient = {

	getList(){
		return axios.post( prefix + "/list" )
	},

	getProductInfo(id){
		return axios.post( prefix + "/info/" + id)
	},

	checkout(payload){
		return axios.post( prefix + "/checkout" , payload)
	}

}

export default ProductClient;