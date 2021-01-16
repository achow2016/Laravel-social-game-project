<template>
	<div class="text-light">

		<div class="row text-center mx-auto">
			<div class="col">
			
				<div class="row mt-5 text-center">
					<div class="col">
						<h2 class="text-center">Login</h2>
					</div>
				</div>
			
				<div class="form-group row">
					<div v-if="!!navError" class="col-sm-8 alert alert-warning" role="alert">
						<span class="text-danger">{{navError}}</span>
					</div>
					<div v-if="errorList.message" class="col-sm-8 alert alert-warning" role="alert">
						<span class="text-danger">{{errorList.message[0]}}</span>
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
				
				<div class="form-group row mt-5 pb-5">
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
				
				<div class="row mt-5">
					<div class="col-sm-8 offset-sm-2">
						<button type="submit" @click.prevent="process" class="loginButton btn btn-dark w-50">Login</button>
					</div>
				</div>

			</div>
				
			<nav class="row fixed-bottom">
				<div class="col btn-group d-flex" role="group">
					<div class="flex-fill w-33">
						<router-link :to="{ name: 'home' }"><button type="button" class="btn btn-dark w-100">Home</button></router-link>
					</div>	
					<div class="flex-fill w-33">
						<router-link :to="{ name: 'register' }"><button type="button" class="btn btn-dark w-100">Register</button></router-link>
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
	
	export default {
		props : ['title','message'],
		data() {
			return {
				email: '',
				password: '',
				errorList: [],
			}
		},
		methods: {
			process() {
				User.login({
					_method: 'POST',
					name: this.name,
					email: this.email,
					password: this.password
				})
				.then((response) => {
					sessionStorage.removeItem('token');
					sessionStorage.setItem('token', response.data.token.plainTextToken);
					this.$router.push('welcome');
				})
				.catch(error => {
					if(error.response.status == 422)
						this.errorList = error.response.data.errors;	
				});
			}
		},
		computed: {
			navError (){
				return this.$route.params.navError;
			}
		}
	};
</script>
<style scoped>
	div button:first-child {
		border: 1px solid white;
	}
</style>