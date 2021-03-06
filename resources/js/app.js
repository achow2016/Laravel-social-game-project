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
import RpgGameBattle from './components/RpgGameBattle'
import Chat from './components/Chat'
import Store from './components/Store'
import MapBuilder from './components/MapBuilder'
import ScoreLister from './components/ScoreLister'
import GuestBook from './components/GuestBook'
import Profile from './components/Profile'
import Sitemap from './components/Sitemap'

//user api for sanctum auth
import User from './apis/User';

//login check that goes to next right away using params returned from user and character check in session controller
function gameCharacterCheck(to, from, next) {
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

//check if logged in and has character
function getCharacterExistenceStatus(to, from, next) {			
	let status = User.getCharacterExistenceStatus({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token'))
		.then((response) => {
			if(response.data.characterStatus == false) {
				return false;
			}
			else
				return true;
		})
		.catch(error => {
			if(error.response.status == 401)
				next({name:'login', params:{navError: 'You must be logged in to access that resource.'}, replace:true});	
			else if(error.response.status == 422)
				return false;
			else
				next({name:'login', params:{navError: 'Could not get user state from database, please create an account or contact admin.'}, replace:true});
		});
	return status;	
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
	to.params.sitemap = routes;
	next(to.params);
}

const router = new VueRouter({
	mode: 'history',
	routes: [
		//home or base url goes to welcome user landing or login page
		{
			path: '/',
			alias: ['/home'],
			name: 'home',
			component: Home,
			props: {}
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
			alias: ['/login'],
			name: 'login',
			component: Login,
			props: {}
		},
		{
			path: '/registerForm',
			alias: ['/register'],
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
			beforeEnter (to, from, next) {
				gameCharacterCheck(to,from,next);
			}
		},
		{
			path: '/characterBuilder',
			name: 'characterBuilder',
			component: CharacterBuilder,
			props: {},
			beforeEnter (to, from, next) {
				gameCharacterCheck(to,from,next);
			}
		},
		{
			path: '/chat',
			name: 'chat',
			component: Chat,
			props: {},
			beforeEnter (to, from, next) {
				gameCharacterCheck(to,from,next);
			}
		},
		{
			path: '/store',
			name: 'store',
			component: Store,
			props: {},
			beforeEnter (to, from, next) {
				gameCharacterCheck(to,from,next);
			}
		},
		{
			path: '/profile',
			name: 'profile',
			component: Profile,
			props: {},
			beforeEnter (to, from, next) {
				gameCharacterCheck(to,from,next);
			}
		},
		{
			path: '/scoreLister',
			name: 'scoreLister',
			component: ScoreLister,
			props: {},
			beforeEnter (to, from, next) {
				gameCharacterCheck(to,from,next);
			}
		},
		{
			path: '/mapBuilder',
			name: 'mapBuilder',
			component: MapBuilder,
			props: {},
			/*
			beforeEnter (to, from, next) {
				gameCharacterCheck(to,from,next);
			}
			*/
		},
		{
			path: '/rpgGame',
			name: 'rpgGame',
			component: RpgGame,
			props: {},
			beforeEnter (to, from, next) {
				
				const headers = { 
				  'Content-Type': 'multipart/form-data',
				  'enctype' : 'multipart/form-data',
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				};
				
				axios({
					method : "POST",
					baseURL: 'http://127.0.0.1:8000/api',
					url    : 'http://127.0.0.1:8000/api/getGameState',
					//params : '',
					//data   : '',
					headers: headers
				}).then(response => {		
					to.params.currentTurn = response.data.currentTurn;
					to.params.enemyTurnPositions = response.data.enemyTurnPositions;
					to.params.playerBattleState = response.data.playerBattleState;
					to.params.playerBattleTarget = response.data.playerBattleTarget;
					to.params.playerGameTurns = response.data.playerGameTurns;
					to.params.playerTurnPosition = response.data.playerTurnPosition;
					to.params.playerAvatar = response.data.playerAvatar;
					to.params.playerVisibleTiles = response.data.playerVisibleTiles;
					
					if(response.data.currentEnemyMapCoord)
						to.params.currentEnemyMapCoord = response.data.currentEnemyMapCoord;
					
					next(to.params);
				}).catch(error => {
					next({name:'login', params:{navError: 'Please login to use this application.'}, replace:true});	
				});
				//next();
			}	
		},
		{
			path: '/rpgGameBattle',
			name: 'rpgGameBattle',
			component: RpgGameBattle,
			props: {},
			
			//beforeEnter (to, from, next) {
			//	
			//}
			
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

//router.beforeEach((to, from, next) => {
	//if (to.name !== 'Login' && !isAuthenticated) next({ name: 'Login' })
	//else next()
//})

//router.afterEach((to, from) => {
  // ...
//})

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
