import Vue from 'vue'
import VueRouter from 'vue-router'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

Vue.use(VueRouter)
Vue.use(BootstrapVue)
//Vue.use(IconsPlugin)

window.$ = window.jQuery = require('jquery')
window.Popper = require('popper.js').default;

import App from './components/App'
import Welcome from './components/Welcome'
import Login from './components/Login'
import Register from './components/Register'
import Home from './components/Home'
import ResetPassword from './components/ResetPassword'
import CharacterBuilder from './components/CharacterBuilder'
import Chat from './components/Chat'
import Store from './components/Store'

//user api for sanctum auth
import User from './apis/User';

function loginCheck(to, from, next) {
	User.getData({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token'))
		.then((response) => {
			to.params.response = response;
			next(to.params);	
		})
		.catch(error => {
			if(error.response.status == 401)
				next({name:'login', params:{navError: 'You must be logged in to access that resource.'}, replace:true});			
			else
				next({name:'login', params:{navError: 'Could not get user state from database, contact administrator.'}, replace:true});
		});
}	

const router = new VueRouter({
	mode: 'history',
	routes: [
		//home or base url goes to welcome user landing or login page
		{
			path: '/',
			name: 'home',
			component: Home,
			props: {}
			/*
			path: '/',
			alias: '/home',
			name: 'home',
			beforeEnter (to, from, next) {
				User.getData({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token'))
				.then((response) => {
					to.params.response = response;
					next({name:'welcome'},to.params);	
				})
				.catch(error => {
					if(error.response.status == 401)
						next({name:'login', params:{navError: 'Please login to use this application.'}, replace:true});			
					else
						next({name:'login', params:{navError: 'unknown error, contact administrator.'}, replace:true});
				});
			}
			*/
		},
		{
			path: '/loginForm',
			name: 'login',
			component: Login,
			props: {}
		},
		{
			path: '/registerForm',
			name: 'register',
			component: Register,
			props: {}
		},	
		{
			path: '/resetPassword',
			name: 'resetPassword',
			component: ResetPassword,
			props: {}
		},
		{
			path: '/welcome',
			name: 'welcome',
			component: Welcome,
			props: {},
			meta: {},
			//before entering route, checks if user is logged in
			beforeEnter (to, from, next) {
				loginCheck(to,from,next);
				/*
				User.getData({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token'))
				.then((response) => {
					to.params.response = response;
					next(to.params);	
				})
				.catch(error => {
					if(error.response.status == 401)
						next({name:'login', params:{navError: 'You must be logged in to access that resource.'}, replace:true});			
					else
						next({name:'login', params:{navError: 'unknown error, contact administrator.'}, replace:true});
				});
				*/
			}
		},
		{
			path: '/characterBuilder',
			name: 'characterBuilder',
			component: CharacterBuilder,
			props: {},
			beforeEnter (to, from, next) {
				loginCheck(to,from,next);
			}
		},
		{
			path: '/chat',
			name: 'chat',
			component: Chat,
			props: {},
			beforeEnter (to, from, next) {
				loginCheck(to,from,next);
			}
		},
		{
			path: '/store',
			name: 'store',
			component: Store,
			props: {},
			beforeEnter (to, from, next) {
				loginCheck(to,from,next);
			}
		},		
		//catch all if non-defined url is entered. Goes to login page or user welcome landing
		/*
		{
			
			path: '*',
			name: 'home',
			component: Home,
			props: {}
			
			beforeEnter (to, from, next) {
				User.getData({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token'))
				.then((response) => {
					to.params.response = response;
					next({name:'welcome'},to.params);	
				})
				.catch(error => {
					if(error.response.status == 401)
						next({name:'login', params:{navError: 'Please login to use this application.'}, replace:true});			
					else
						next({name:'login', params:{navError: 'unknown error, contact administrator.'}, replace:true});
				});
			}
			
		}
		*/
	],
})
const app = new Vue({
	el: '#app',
	components: { App },
	router,
});