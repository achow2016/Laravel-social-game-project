<template>
	<div class="text-light">
		<nav class="row">
			<div class="col btn-group d-flex mb-4" role="group">
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'home' }"><button type="button" class="btn btn-dark w-100">Home</button></router-link>
				</div>	
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'login' }"><button type="button" class="btn btn-dark w-100">Login</button></router-link>
				</div>	
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'register' }"><button type="button" class="btn btn-dark w-100">Register</button></router-link>
				</div>
			</div>				
		</nav>
		
		<div class="row text-center">
			<div class="col">
				<div class="row text-center mb-4">
					<div class="col">
						<h1>Reset Password</h1>
					</div>
				</div>
				<div v-if="!!message" class="row-sm-8 alert alert-warning" role="alert">
					<div class="col">
						<span class="text-danger">{{message}}</span>
					</div>
				</div>	
				<div class="form-group row mb-4">
					<div class="col-sm-3">
						<label for="email">Email:</label>
					</div>
					<div class="col-sm-8">
						<input v-model="email" type="email" class="form-control" id="email">
					</div>
				</div>
				<div id="canvasSite" class="form-group row mb-2">
					<div class="col">
						<canvas ref="canvas" width="100" height="100"></canvas>
					</div>
				</div>
				<div class="form-group row mb-4">
					<div class="col-sm-3">
						<label for="challangeCode">Code:</label>
					</div>
					<div class="col-sm-8">
						<input v-model="code" type="text" class="form-control" id="challangeCode">
					</div>
				</div>
				<div class="row">
					<div class="col-sm-8 offset-sm-2">
						<button type="submit" @click.prevent="process" class="loginButton btn btn-dark w-50">Process</button>
					</div>
				</div>			
			</div>
		</div>
	</div>	
</template>
<script>
	import User from '../apis/User';
			
	export default {
		data: function() {
			return {
				email: '',
				code: '',
				randomNumber: '',
				message: '',
				provider: {
					context: null
				}
			}
		},
		provide () {
			return {
				provider: this.provider
			}
		},
		mounted() {
			this.generateCode();
			if(this.$route.query.message != null)
				this.message = this.$route.query.message;
		},
		methods: {
			generateCode() {
				this.randomNumber = Math.floor(Math.random() * 100) + 1;
				let ctx = this.provider.context;
				ctx = this.$refs['canvas'].getContext('2d');
				ctx.clearRect(0, 0, 100, 100);
				ctx.font = "30px Arial";
				ctx.fillStyle = "white";
				ctx.textAlign = "center";
				ctx.fillText(this.randomNumber, 50, 50);
			},
			process() {
				if(this.code == this.randomNumber) {
					User.resetPassword({
						_method: 'POST',
						email: this.email
					})
					.then((response) => {
						this.message = 'Please check email for the password reset link.';
						this.email = '';
						this.code = '';
						this.generateCode();
					})
					.catch(error => {
						if(error.response.status == 422)
							this.errorList = error.response.data.errors;	
					});
				}
				else {
					this.message = 'Please check and re-enter code.';
					this.email = '';
					this.code = '';
					this.generateCode();
				}
			}
		}
	};
</script>
<style scoped>
	div button:first-child {
		border: 1px solid white;
	}
</style>