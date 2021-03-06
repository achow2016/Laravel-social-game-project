/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('home', require('./components/Home.vue').default);
import Vue from 'vue'
import VueRouter from 'vue-router'
import { BootstrapVue, BootstrapVueIcons} from 'bootstrap-vue'

Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
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
import RpgGame from './components/RpgGame'
import Chat from './components/Chat'
import Store from './components/Store'
import MapBuilder from './components/MapBuilder'
import GuestBook from './components/GuestBook'
import Profile from './components/Profile'
import Sitemap from './components/Sitemap'

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
				next({name:'login', params:{navError: 'Could not get user state from database, please create an account or contact admin.'}, replace:true});
		});
}

function recordGuest(to, from, next) {
	User.recordGuest({_method: 'GET'})
		.then((response) => {
			to.params.response = response;
			next(to.params);	
		})
		.catch(error => {
			if(error.response.status == 422)
				next({name:'home', params:{navError: 'Database error, could not record guest.'}, replace:true});
		});
}

function getSitemap(to, from, next, routes, pre) {
	
	console.log(routes);
	
	//return array;
	//next({name:'sitemap', params:{routes: routes}});	
	//next();	
	to.params.sitemap = routes;
	next(to.params);

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
			path: '/guestbook',
			name: 'guestbook',
			component: GuestBook,
			props: {},
			beforeEnter (to, from, next) {
				recordGuest(to,from,next);
			}
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
		{
			path: '/profile',
			name: 'profile',
			component: Profile,
			props: {},
			beforeEnter (to, from, next) {
				loginCheck(to,from,next);
			}
		},		
		{
			path: '/mapBuilder',
			name: 'mapBuilder',
			component: MapBuilder,
			props: {},
			beforeEnter (to, from, next) {
				loginCheck(to,from,next);
			}
		},
		{
			path: '/rpgGame',
			name: 'rpgGame',
			component: RpgGame,
			props: {},
			beforeEnter (to, from, next) {
				loginCheck(to,from,next);
			}
		},
		{
			path: '/sitemap',
			name: 'sitemap',
			component: Sitemap,
			props: {},
			beforeEnter (to, from, next) {
				getSitemap(to, from, next, router.options.routes, 'http://127.0.0.1:8000');
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
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//const app = new Vue({
//    el: '#app',
//});
