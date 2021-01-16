<template>
	<div>
		<div class="row text-center text-light mx-auto">
			<div class="col">
			
					<div class="row mt-5">
						<div class="col">
							<h2 class="text-center">Register</h2>
						</div>
					</div>
				
					<div class="row" v-if="!!message">
						<span v-if="!!message" class="col-sm-8 alert alert-warning" role="alert">{{message}}</span>
					</div>
					
					<div class="form-group row mt-5">
						<div class="col-sm-3">
							<label for="name">Name:</label>
						</div>
						<div v-if="errorList.name" class="col-sm-8 alert alert-warning" role="alert">
							<input type="text" v-model="name" class="form-control" id="name">
							<span class="text-danger">{{errorList.name[0]}}</span>
						</div>	
						<div class="col-sm-8" v-else>
							<input type="text" v-model="name" class="form-control" id="name">
						</div>
					</div>
					
					<div class="form-group row mt-5">
						<div class="col-sm-3">
							<label for="email">Email:</label>
						</div>
						<div v-if="errorList.email" class="col-sm-8 alert alert-warning" role="alert">
							<input type="email" v-model="email" class="form-control" id="email">
							<span class="text-danger">{{errorList.email[0]}}</span>
						</div>	
						<div v-else class="col-sm-8">
							<input type="email" v-model="email" class="form-control" id="email">
						</div>
					</div>

					<div class="form-group row mt-5">
						<div class="col-sm-3">
							<label for="password">Password:</label>
						</div>
						<div v-if="errorList.password" class="col-sm-8 alert alert-warning" role="alert">
							<input type="password" v-model="password" class="form-control" id="password">
							<span class="text-danger">{{errorList.password[0]}}</span>
						</div>	
						<div v-else class="col-sm-8">
							<input type="password" v-model="password" class="form-control" id="password">
						</div>
					</div>
					
					<div class="form-group row mt-5 mb-5">
						<div class="col-sm-3">
							<label for="password">Password Again:</label>
						</div>
						<div v-if="errorList.password" class="col-sm-8 alert alert-warning" role="alert">
							<input type="password" v-model="password_confirmation" class="form-control" id="password_confirmation">
						</div>	
						<div v-else class="col-sm-8">
							<input type="password" v-model="password_confirmation" class="form-control" id="password_confirmation">
						</div>
					</div>

					<div class="row">
						<div class="col-sm-8 offset-sm-2">
							<button type="submit" @click.prevent="process" class="btn btn-dark w-50">Register</button>
						</div>
					</div>
					
					</div>
				</div>
				
				<nav class="row fixed-bottom">
					<div class="col btn-group d-flex" role="group">
						<div class="flex-fill w-33">
							<router-link :to="{ name: 'home' }"><button type="button" class="btn btn-dark w-100">Home</button></router-link>
						</div>	
						<div class="flex-fill w-33">
							<router-link :to="{ name: 'login' }"><button type="button" class="btn btn-dark w-100">Login</button></router-link>
						</div>	
						<div class="flex-fill w-33">
							<router-link :to="{ name: 'resetPassword' }"><button type="button" class="btn btn-dark w-100">Reset</button></router-link>
						</div>
					</div>				
				</nav>
				
			</div>
		</div>
</template>
<script>
	import User from '../apis/User';
	import Csrf from '../apis/Csrf';
	
	export default {
		props : ['title'],
		data() {
			return {
				name: '',
				email: '',
				password: '',
				password_confirmation: '',		
				errorList: [],
				message: ''
			}	
		},
		methods: {
			process() {
				Csrf.getCookie().then(() => {
					User.register({
						name: this.name,
						email: this.email,
						password: this.password,
						password_confirmation: this.password_confirmation
					})
					.then(response => {
						sessionStorage.removeItem('token');
						sessionStorage.setItem('token', response.data.token.plainTextToken);
						this.$router.push('loginForm');
					})
					.catch(error => {
						if(error.response.status == 422)
							this.errorList = error.response.data.errors;
					});
				});
			}
		}
	};
</script>
<style scoped>
	div button:first-child {
		border: 1px solid white;
	}
</style>