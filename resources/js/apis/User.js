import Api from './Api';
import Csrf from './Csrf';

export default {
	/*
		guest user utilities
	*/
	async recordGuest() {
		//await Csrf.getCookie();
		return Api.get('/recordGuest');
	},
	async getGuestbookNotes() {
		return Api.get('/getGuestbookNotes');
	},
	async addGuestbookNote(form) {
		return Api.post('/addGuestbookNote', form);
	},
	
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
		//await Csrf.getCookie();
		return Api.post('/getData',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});	
	},
	async logout(form, token) { 
		//await Csrf.getCookie();
		return Api.post('/logout',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});	
	},
	async resetPassword(form) {
		//await Csrf.getCookie();
		return Api.post('/resetPassword', form);
	},
	async getUserProfile(form, token) {
		//await Csrf.getCookie();
		return Api.post('/getUserProfile',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	/*
		chat, message board
	*/
	async getPosts(form, token) {
		//await Csrf.getCookie();
		return Api.post('/getPosts',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async getReplies(form, token) {
		//await Csrf.getCookie();
		return Api.post('/getReplies',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async makePostReply(form, token) {
		//await Csrf.getCookie();
		return Api.post('/makePostReply',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async makePost(form, token) {
		//await Csrf.getCookie();
		return Api.post('/makePost',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	/*
		game
	*/
	async createCharacter(form, token) {
		//await Csrf.getCookie();
		return Api.post('/createCharacter',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async getCharacterStatus(form, token) {
		//await Csrf.getCookie();
		return Api.post('/getCharacterStatus',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},	
	async generateMap(form, token) {
		//await Csrf.getCookie();
		return Api.post('/generateMap',form,{headers: {
			//'Content-type' : 'application/json',
			'Content-Type':'application/x-www-form-urlencoded',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async generateEnemies(form, token) {
		//await Csrf.getCookie();
		return Api.post('/generateEnemies',form,{headers: {
			//'Content-type' : 'application/json',
			'Content-Type':'application/x-www-form-urlencoded',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async getEnemies(form, token) {
		//await Csrf.getCookie();
		return Api.post('/getEnemies',form,{headers: {
			//'Content-type' : 'application/json',
			'Content-Type':'application/x-www-form-urlencoded',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async inspectEnemies(form, token) {
		//await Csrf.getCookie();
		return Api.post('/inspectEnemies',form,{headers: {
			//'Content-type' : 'application/json',
			'Content-Type':'application/x-www-form-urlencoded',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async getMap(form, token) {
		//await Csrf.getCookie();
		return Api.post('/getMap',form,{headers: {
			//'Content-type' : 'application/json',
			'Content-Type':'application/x-www-form-urlencoded',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async moveCharacter(form, token) {
		//await Csrf.getCookie();
		return Api.post('/moveCharacter',form,{headers: {
			//'Content-type' : 'application/json',
			'Content-Type':'application/x-www-form-urlencoded',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async updateProfileVideo(form, token) {
		//await Csrf.getCookie();
		return Api.post('/updateProfileVideo',form,{headers: {
			//'Content-type' : 'multipart/form-data',
			//'Content-type' : 'application/x-www-form-urlencoded',
			//'Content-Type': 'multipart/form-data',
			//'enctype' : 'multipart/form-data',
			//'boundary' : Math.random().toString().substr(2),
			'Authorization': `Bearer ${token}` 
		}});
	},
	async updateName(form, token) {
		//await Csrf.getCookie();
		return Api.post('/updateName',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async updateEmail(form, token) {
		//await Csrf.getCookie();
		return Api.post('/updateEmail',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async updatePassword(form, token) {
		//await Csrf.getCookie();
		return Api.post('/updatePassword',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	/*
		store
	*/
	async getStoreItems(form, token) {
		//await Csrf.getCookie();
		return Api.post('/getStoreItems',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async addCartItem(form, token) {
		//await Csrf.getCookie();
		return Api.post('/addCartItem',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async getCartItems(form, token) {
		//await Csrf.getCookie();
		return Api.post('/getCartItems',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async deleteCartItem(form, token) {
		//await Csrf.getCookie();
		return Api.post('/deleteCartItem',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async updateCartItem(form, token) {
		//await Csrf.getCookie();
		return Api.post('/updateCartItem',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	async checkout(form, token) {
		//await Csrf.getCookie();
		return Api.post('/checkout',form,{headers: {
			'Content-type' : 'application/json',
			'Authorization': `Bearer ${token}` 
		}});
	},
	
}