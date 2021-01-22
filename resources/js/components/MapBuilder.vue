<template>
    <div class="d-flex flex-column text-light">
		<header class="row">
			<div class="col text-center d-flex">		
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
				</div>	
				<div class="flex-fill w-33 h-75">
					<h3 class="mt-1">Map Builder</h3>
				</div>	
				<div class="flex-fill w-33">
					<button v-on:click="logout" type="button" class="btn btn-dark flex-fill w-100">Logout</button>
				</div>	
			</div>
		</header>
	
		<div class="text-center mt-2 mb-2">
			<div class="col">
				<h5>Rpg game map builder</h5>
			</div>
		</div>
		<div class="text-light">
			<div class="col">		
				
				
				<div class="row">
					<div class="col text-center">
						<br>
						<p>Generating map.</p>
					</div>					
				
				</div>
					
			</div>
		</div>	
    </div>
</template>
<script>
	import User from '../apis/User';
	import Csrf from '../apis/Csrf';
	export default {
		props : [],
		data() {
			return {
				
			}
		},
		mounted() { 
			User.generateMap({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
				
				});
		},
		methods: {
			logout() {
				User.logout({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					sessionStorage.removeItem('token');
					this.$router.push('loginForm');
				});
			},
		}
	}
</script>
<style scoped>
</style>