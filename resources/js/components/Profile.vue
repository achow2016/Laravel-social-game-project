<template>
    <div class="text-light">
		<div class="row mx-auto">
			<div class="col">
				
				<header class="row fixed-top">
					<div class="col text-center d-flex">		
						<div class="flex-fill w-33">
							<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
						</div>	
						<div class="flex-fill w-33 h-75">
							<h3 class="mt-1">Profile</h3>
						</div>	
						<div class="flex-fill w-33">
							<button v-on:click="logout" type="button" class="btn btn-dark flex-fill w-100">Logout</button>
						</div>	
					</div>
				</header>
				
				<div v-if="!!sysError" class="row text-center mt-2 mb-2">
					<div class="col-sm-8 alert alert-warning" role="alert">
						<span class="text-danger">{{sysError}}</span>
					</div>
				</div>
				<div v-if="errorList.addText || errorList.removeText" class="col-sm-8 alert alert-warning text-center" role="alert">
					<span v-if="errorList.addText" class="text-danger">{{errorList.addText[0]}}</span>
					<span v-if="errorList.removeText" class="text-danger">{{errorList.removeText[0]}}</span>
				</div>
				
				<section class="row text-center mt-5 mb-2">
					<div class="col">
						<div v-if="!!userData" class="col-sm-12">
							<div v-for="(value, key) in userData" class="row">
								<div class="w-100 bg-secondary mb-3">
									<div class="row mr-0">
										<div class="col-10">
											<span class="text-center">{{key}}</span>
										</div>
										<div class="col-2">
											<button v-bind:class="key" v-if="['avatar','name','email','credits','membership'].indexOf(key) > -1" v-on:click="openSection(key)" class="float-right border border-success badge badge-pill badge-secondary align-middle">							
												<b-icon-chevron-bar-expand v-bind:class="key" ></b-icon-chevron-bar-expand>
											</button>
											<button v-else v-bind:class="key" v-on:click="" class="float-right border border-danger badge badge-pill badge-secondary align-middle">
												<b-icon-x-octagon></b-icon-x-octagon>
											</button>
										</div>
									</div>
									<div class="row">
										<div class="col">
											{{value}}
										</div>
									</div>
									<div v-if="key == 'avatar'" id="avatarForm" class="d-none row">
										<div class="col">
											avatar form
										</div>
									</div>
									<div v-if="key == 'name'" id="nameForm" class="d-none row">
										<div class="col">
											name form
										</div>
									</div>
									<div v-if="key == 'email'" id="emailForm" class="d-none row">
										<div class="col">
											email form
										</div>
									</div>
									<div v-if="key == 'credits'" id="creditsForm" class="d-none row">
										<div class="col">
											credits form
										</div>
									</div>
									<div v-if="key == 'membership'" id="membershipForm" class="d-none row">
										<div class="col">
											memebrship form
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				
				
							
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
				errorList: [],
				userData: ''
			}
		},
		mounted() { 
			Csrf.getCookie().then(() => {
					User.getUserProfile({
						_method: 'POST',
						token: sessionStorage.getItem('token')
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						console.log(response);
						this.userData = response.data.userData;
					})
					.catch(error => {
					
					});
				});
		},		
		methods: {
			logout() {
				User.logout({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token')).then((response) => {
					sessionStorage.removeItem('token');
					this.$router.push('loginForm');
				});
			},
			openSection(key) {
				console.log(key);
				let section = key;
				switch(section) {
					case 'avatar':
						document.querySelector('#avatarForm').classList.toggle('d-none');
						break;
					case 'name':
						document.querySelector('#nameForm').classList.toggle('d-none');
						break;
					case 'credits':
						document.querySelector('#creditsForm').classList.toggle('d-none');
						break;
					case 'email':
						document.querySelector('#emailForm').classList.toggle('d-none');
						break;
					case 'membership':
						document.querySelector('#membershipForm').classList.toggle('d-none');
						break;	
					default:
						break;
				}
			}
		},
		computed: {
			sysError (){
				return this.$route.params.sysError;
			}
		}
	}
</script>
<style scoped>
	.bg-secondary {
		opacity: 0.85;
	}
</style>