import axios from 'axios';
const URL = 'http://127.0.0.1:8000/api';
//const URL = 'http://localhost:8000/api';

let Api = axios.create({
	baseURL: URL
});	

Api.defaults.withCredentials = true;

export default Api;