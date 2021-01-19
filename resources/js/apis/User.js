import Api from './Api';
import Csrf from './Csrf';

export default {
	/*
		user session, registration
	*/
	async register(form) {
		await Csrf.getCookie();
		return Api.post('/register', form);
	},
	async login(form) {
		await Csrf.getCookie();
		return Api.post('/login', form);
	},	
	async getData(form, token) { 
		await Csrf.getCookie();
		return Api.post('/getData',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});	
	},
	async logout(form, token) { 
		await Csrf.getCookie();
		return Api.post('/logout',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});	
	},
	async resetPassword(form) {
		await Csrf.getCookie();
		return Api.post('/resetPassword', form);
	},
	/*
		chat, message board
	*/
	async getPosts(form, token) {
		await Csrf.getCookie();
		return Api.post('/getPosts',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async getReplies(form, token) {
		await Csrf.getCookie();
		return Api.post('/getReplies',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async makePostReply(form, token) {
		await Csrf.getCookie();
		return Api.post('/makePostReply',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async makePost(form, token) {
		await Csrf.getCookie();
		return Api.post('/makePost',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	/*
		game
	*/
	async createCharacter(form, token) {
		await Csrf.getCookie();
		return Api.post('/createCharacter',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	/*
		store
	*/
	async getStoreItems(form, token) {
		await Csrf.getCookie();
		return Api.post('/getStoreItems',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async addCartItem(form, token) {
		await Csrf.getCookie();
		return Api.post('/addCartItem',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async getCartItems(form, token) {
		await Csrf.getCookie();
		return Api.post('/getCartItems',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async cartRemoveItem(form, token) {
		await Csrf.getCookie();
		return Api.post('/cartRemoveItem',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async cartModifyItem(form, token) {
		await Csrf.getCookie();
		return Api.post('/cartModifyItem',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async checkout(form, token) {
		await Csrf.getCookie();
		return Api.post('/checkout',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	
}