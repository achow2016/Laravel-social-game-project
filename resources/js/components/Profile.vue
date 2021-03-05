<template>
    <div class="text-light">
		<div class="row mx-auto">
			<div class="col">
				
				<header id="headerMenu" class="row fixed-top">
					<div class="col text-center d-flex">		
						<div class="flex-fill w-33">
							<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
						</div>	
						<div class="flex-fill w-33 h-100 bg-secondary">
							<h3>Profile</h3>
						</div>	
						<div class="flex-fill w-33">
							<button v-on:click="logout" type="button" class="btn btn-dark flex-fill w-100">Logout</button>
						</div>	
					</div>
				</header>
				
				<header id="headerMessage" class="row fixed-top d-none">
					<div class="col text-center d-flex">		
						<div id="errorMessage" class="flex-fill w-33">
							
						</div>	
					</div>
				</header>
				
				<div v-if="!!sysError" class="d-none row text-center mt-2 mb-2">
					<div class="col-sm-8 alert alert-warning" role="alert">
						<span class="text-danger">{{sysError}}</span>
					</div>
				</div>
				<div v-if="!!errorList" class="d-none col-sm-8 alert alert-warning text-center" role="alert">
					<span v-if="errorList.addText" class="text-danger">{{errorList.addText[0]}}</span>
					<span v-if="errorList.removeText" class="text-danger">{{errorList.removeText[0]}}</span>
				</div>
				
				<section class="row text-center mt-5 mb-2">
					<div class="col">
						<div v-if="!!userData" class="col-sm-12">
							<div v-for="(value, key) in userData" class="row">
								<div class="w-100 bg-secondary mb-3">
									<div class="row mr-0">
										<div class="col-10 text-left">
											<span v-if="key == 'account_verified_date'" class="pl-3">account verified on</span>
											<span v-else-if="key == 'membership_start_date'" class="pl-3">membership started</span>
											<span v-else-if="key == 'membership_end_date'" class="pl-3">membership ends</span>
											<span v-else-if="key == 'created_at'" class="pl-3">account created</span>
											<span v-else-if="key == 'updated_at'" class="pl-3">account updated</span>
											<span v-else-if="key == 'profile_video'" class="pl-3">profile video</span>
											<span v-else-if="key == 'profile_image'" class="pl-3">profile avatar</span>
											<span v-else class="pl-3">{{key}}</span>
										</div>
										<div class="col-2">
											<button v-bind:class="key" v-if="['avatar','name','email','credits','membership','profile_video','profile_image'].indexOf(key) > -1" v-on:click="openSection(key)" class="float-right border border-success badge badge-pill badge-secondary align-middle">							
												<b-icon-chevron-bar-expand v-bind:class="key" ></b-icon-chevron-bar-expand>
											</button>
											<button v-else v-bind:class="key" v-on:click="" class="float-right border border-danger badge badge-pill badge-secondary align-middle">
												<b-icon-x-octagon></b-icon-x-octagon>
											</button>
										</div>
									</div>
									<div class="row ml-0 mr-0 bg-dark">
										<div v-if="key == 'membership' && value == 0" class="col">
											none
										</div>
										<div v-else-if="key == 'membership' && value == 1" class="col">
											member
										</div>
										<div v-else-if="key == 'created_at'" class="col">
											{{value | cleanLaravelDate}} 
										</div>
										<div v-else-if="key == 'updated_at'" class="col">
											{{value | cleanLaravelDate}} 
										</div>
										<div v-else-if="key == 'profile_video' && value != null"" class="col">
											Video Set 
										</div>
										<div v-else-if="key == 'profile_video' && value == null"" class="col">
											No Video  
										</div>
										<div v-else-if="key == 'profile_image' && value != null"" class="col">
											Avatar Set 
										</div>
										<div v-else-if="key == 'profile_image' && value == null"" class="col">
											No Avatar  
										</div>
										
										<div v-else class="col">
											<div v-bind:id="key">
												{{value}}
											</div>
										</div>
									</div>
									
									<div v-if="key == 'name'" id="nameForm" class="d-none row ml-0 mr-0 bg-info">
										<div class="col">
											<div class="row">
												<div class="col-10">
													<input class="mt-1 mb-1" type="text" name="newName" id="newName" placeholder="enter new name"></input>
												</div>
											</div>
											<div class="row">
												<div class="col-10">		
													<input class="mt-1 mb-1" type="text" name="nameConfirmation" id="nameConfirmation" placeholder="confirm new name"></input>
												</div>
												<div class="col-2 mb-1 mt-1 float-right">
													<button v-on:click="updateName()" class="float-right border border-danger badge badge-pill badge-secondary mt-1 mb-1">
														<b-icon-brush></b-icon-brush>
													</button>
												</div>	
											</div>
										</div>
									</div>
									<div v-if="key == 'email'" id="emailForm" class="d-none row ml-0 mr-0 bg-info">
										<div class="col">
											<div class="row">
												<div class="col-10">
													<input class="mt-1 mb-1" type="text" name="newEmail" id="newEmail" placeholder="enter new email"></input>
												</div>
											</div>
											<div class="row">
												<div class="col-10">		
													<input class="mt-1 mb-1" type="text" name="emailConfirmation" id="emailConfirmation" placeholder="confirm new email"></input>
												</div>
												<div class="col-2 mb-1 mt-1 float-right">
													<button v-on:click="updateEmail()" class="float-right border border-danger badge badge-pill badge-secondary mt-1 mb-1">
														<b-icon-brush></b-icon-brush>
													</button>
												</div>	
											</div>
										</div>
									</div>
									<div v-if="key == 'credits'" id="creditsForm" class="d-none row ml-0 mr-0 bg-info">
										<div class="col">
											credits form
										</div>
									</div>
									<div v-if="key == 'membership'" id="membershipForm" class="d-none row ml-0 mr-0 bg-info">
										<div class="col">
											memebrship form
										</div>
									</div>
									<div v-if="key == 'profile_video'" id="profileVideoForm" class="d-none row ml-0 mr-0 bg-info">
										<div class="col">
										
											<div class="row mt-3">
												<div class="col">
													<video src="" class="embed-responsive" id="profileVideo" width="420">
													Your browser does not support HTML video.
													</video>
													<button v-on:click="playPauseProfileVideo()">Play/Pause</button> 
												</div>
											</div>

											<div class="row">
												<div class="col">
													<input @change="processVideoFile" class="mb-1 mt-1" type="file" id="profileVideoFile" name="profileVideoFile">
													<button type="submit" @click.prevent="updateProfileVideo" class="mb-1 mt-1">Upload</button>
												</div>
											</div>
										</div>	
									</div>
									<div v-if="key == 'profile_image'" id="profileImageForm" class="d-none row ml-0 mr-0 bg-info">
										<div class="col">
										
											<div class="row mt-3">
												<div class="col">
													<img class="img-fluid" src="" id="profileImage">
												</div>
											</div>

											<div class="row">
												<div class="col">
													<input @change="processImageFile" class="mb-1 mt-1" type="file" id="profileImageFile" name="profileImageFile">
													<button type="submit" @click.prevent="updateProfileImage" class="mb-1 mt-1">Upload</button>
												</div>
											</div>
										</div>	
									</div>
									
								</div>
							</div>
							
							<div class="row">
								<div class="w-100 bg-secondary mb-3">
									<div class="row mr-0">
										<div class="col-10 text-left">
											<span class="pl-3">password</span>											
										</div>
										<div class="col-2">
											<button v-on:click="expandPasswordMenu()" class="float-right border border-success badge badge-pill badge-secondary align-middle">							
												<b-icon-chevron-bar-expand></b-icon-chevron-bar-expand>
											</button>
										</div>
									</div>
									<div id="passwordForm" class="d-none row ml-0 mr-0 bg-info">
										<div class="col">
											<div class="row">
												<div class="col-10">
													<input class="mt-1 mb-1" type="text" name="oldPassword" id="oldPassword" placeholder="enter current password"></input>
												</div>
											</div>
											<div class="row">
												<div class="col-10">
													<input class="mt-1 mb-1" type="text" name="newPassword" id="newPassword" placeholder="enter new password"></input>
												</div>
											</div>
											<div class="row">
												<div class="col-10">		
													<input class="mt-1 mb-1" type="text" name="passwordConfirmation" id="passwordConfirmation" placeholder="confirm new password"></input>
												</div>
												<div class="col-2 mb-1 mt-1 float-right">
													<button v-on:click="updatePassword()" class="float-right border border-danger badge badge-pill badge-secondary mt-1 mb-1">
														<b-icon-brush></b-icon-brush>
													</button>
												</div>	
											</div>
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
				userData: '',
				name: '',
				email: '',
				newPassword: '',
				currentPassword: '',
				profileVideo: '',
				profileImage: '',
				formData: '',
				name: '',
				nameConfirmation: ''
			}
		},
		filters: {
			cleanLaravelDate: 
				function(value) {
					return value.substring(0,10);
				}
		},
		mounted() {
			//Csrf.getCookie().then(() => {
					User.getUserProfile({
						_method: 'POST',
						token: sessionStorage.getItem('token')
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						this.userData = response.data.userData;
					})
					.catch(error => {
					
					});
			//	});
		},		
		methods: {
			getUserData() {
				//Csrf.getCookie().then(() => {
					User.getUserProfile({
						_method: 'POST',
						token: sessionStorage.getItem('token')
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						this.userData = response.data.userData;
					})
					.catch(error => {
					
					});
				//});
			},
			getUserVideo() {
				return this.userData['profile_video'];
			},
			getUserImage() {
				return this.userData['profile_image'];
			},
			updateName() {
				this.name = document.querySelector('#newName').value;
				this.nameConfirmation = document.querySelector('#nameConfirmation').value;
				
				//client side validation
				if(this.name == '' || this.nameConfirmation == '') {
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-danger');
					document.querySelector('#errorMessage').textContent = 'name fields incomplete';
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					return;
				}	
				
				if(this.name != this.nameConfirmation) {
					document.querySelector('#newName').value = '';
					document.querySelector('#nameConfirmation').value = '';
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-danger');
					document.querySelector('#errorMessage').textContent = 'name fields don\'t match';
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					return;
				}
				
				User.updateName({
					_method: 'POST', 
					token: sessionStorage.getItem('token'),
					name: this.name,
					name_confirmation: this.nameConfirmation
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-success');
					document.querySelector('#errorMessage').textContent = response.data.status;
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-success');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					//new value shown with colour queue
					document.querySelector('#newName').value = '';
					document.querySelector('#nameConfirmation').value = '';
					document.querySelector('#name').textContent = response.data.name;
					document.querySelector('#name').classList.toggle('bg-success');
					setTimeout(function(){ 
						document.querySelector('#name').classList.toggle('bg-success');
					}, 500);
				}).catch(error => {
					//server response errors
					if(error.response) {
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = error.response.data.status;
						setTimeout(function(){ 
							document.querySelector('#headerMenu').classList.toggle('d-none');
							document.querySelector('#headerMessage').classList.toggle('d-none');
							document.querySelector('#headerMessage').classList.toggle('bg-danger');
							document.querySelector('#errorMessage').textContent = '';
						}, 3000);
					} 
					//for no response	
					else if(error.request) {
						// The request was made but no response was received
						console.log(error.request);
					} 
					//catch outside above cases
					else {
						console.log('Error', error.message);
					}
				});	
			},
			updateEmail() {
				this.email = document.querySelector('#newEmail').value;
				this.emailConfirmation = document.querySelector('#emailConfirmation').value;
				
				//client side validation
				if(this.email == '' || this.emailConfirmation == '') {
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-danger');
					document.querySelector('#errorMessage').textContent = 'email fields incomplete';
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					return;
				}	
				
				if(this.email != this.emailConfirmation) {
					document.querySelector('#newEmail').value = '';
					document.querySelector('#emailConfirmation').value = '';
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-danger');
					document.querySelector('#errorMessage').textContent = 'email fields don\'t match';
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					return;
				}
				
				User.updateEmail({
					_method: 'POST', 
					token: sessionStorage.getItem('token'),
					email: this.email,
					email_confirmation: this.emailConfirmation
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-success');
					document.querySelector('#errorMessage').textContent = response.data.status;
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-success');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					//new value shown with colour queue
					document.querySelector('#newEmail').value = '';
					document.querySelector('#emailConfirmation').value = '';
					document.querySelector('#email').textContent = response.data.email;
					document.querySelector('#email').classList.toggle('bg-success');
					setTimeout(function(){ 
						document.querySelector('#email').classList.toggle('bg-success');
					}, 500);
				}).catch(error => {
					//server response errors
					if(error.response) {
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = error.response.data.status;
						setTimeout(function(){ 
							document.querySelector('#headerMenu').classList.toggle('d-none');
							document.querySelector('#headerMessage').classList.toggle('d-none');
							document.querySelector('#headerMessage').classList.toggle('bg-danger');
							document.querySelector('#errorMessage').textContent = '';
						}, 3000);
					} 
					//for no response	
					else if(error.request) {
						// The request was made but no response was received
						console.log(error.request);
					} 
					//catch outside above cases
					else {
						console.log('Error', error.message);
					}
				});	
			},
			updatePassword() {
				this.oldPassword = document.querySelector('#oldPassword').value;
				this.password = document.querySelector('#newPassword').value;
				this.passwordConfirmation = document.querySelector('#passwordConfirmation').value;
				
				//client side validation
				if(this.oldPassword == '' || this.password == '' || this.passwordConfirmation == '') {
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-danger');
					document.querySelector('#errorMessage').textContent = 'password fields incomplete';
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					return;
				}	
				
				if(this.password != this.passwordConfirmation) {
					document.querySelector('#oldPassword').value = '';
					document.querySelector('#newPassword').value = '';
					document.querySelector('#passwordConfirmation').value = '';
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-danger');
					document.querySelector('#errorMessage').textContent = 'password fields don\'t match';
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					return;
				}
				
				User.updatePassword({
					_method: 'POST', 
					token: sessionStorage.getItem('token'),
					oldPassword: this.oldPassword,
					password: this.password,
					password_confirmation: this.passwordConfirmation
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					//top header message
					document.querySelector('#headerMenu').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('d-none');
					document.querySelector('#headerMessage').classList.toggle('bg-success');
					document.querySelector('#errorMessage').textContent = response.data.status;
					setTimeout(function(){ 
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-success');
						document.querySelector('#errorMessage').textContent = '';
					}, 3000);
					//no password shown
					document.querySelector('#oldPassword').value = '';
					document.querySelector('#newPassword').value = '';
					document.querySelector('#passwordConfirmation').value = '';
				}).catch(error => {
					//server response errors
					if(error.response) {
						console.log(error.response.data.status);
						document.querySelector('#headerMenu').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('d-none');
						document.querySelector('#headerMessage').classList.toggle('bg-danger');
						document.querySelector('#errorMessage').textContent = error.response.data.status;
						setTimeout(function(){ 
							document.querySelector('#headerMenu').classList.toggle('d-none');
							document.querySelector('#headerMessage').classList.toggle('d-none');
							document.querySelector('#headerMessage').classList.toggle('bg-danger');
							document.querySelector('#errorMessage').textContent = '';
						}, 3000);
					} 
					//for no response	
					else if(error.request) {
						// The request was made but no response was received
						console.log(error.request);
					} 
					//catch outside above cases
					else {
						console.log('Error', error.message);
					}
				});	
			},
			expandPasswordMenu() {
				document.querySelector('#passwordForm').classList.toggle('d-none');
			},
			expandProfileVideoMenu() {
				document.querySelector('#profileVideoForm').classList.toggle('d-none');
			},
			processVideoFile(event) {
				this.profileVideo = event.target.files[0];
			},
			processImageFile(event) {
				this.profileImage = event.target.files[0];
			},
			updateProfileVideo() {
				this.formData = new FormData();
				this.formData.append('profileVideo', this.profileVideo);
				this.formData.append('_method', 'POST');
				
				const headers = { 
				  'Content-Type': 'multipart/form-data',
				  'enctype' : 'multipart/form-data',
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				}
				axios({
					method : "POST",
					baseURL: 'http://127.0.0.1:8000/api',
					url    : 'http://127.0.0.1:8000/api/updateProfileVideo',
					params : '',
					data   : this.formData,
					headers: headers,
				}).then(response => {
					location.reload();
				})
			},
			updateProfileImage() {
				this.formData = new FormData();
				this.formData.append('profileImage', this.profileImage);
				this.formData.append('_method', 'POST');
	
				const headers = { 
				  'Content-Type': 'multipart/form-data',
				  'enctype' : 'multipart/form-data',
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				}
				axios({
					method : "POST",
					baseURL: 'http://127.0.0.1:8000/api',
					url    : 'http://127.0.0.1:8000/api/updateProfileImage',
					params : '',
					data   : this.formData,
					headers: headers,
				}).then(response => {
					location.reload();
				})
			},
			updateMembership() {
				
			},
			updateCredits() {
				
			},
			playPauseProfileVideo() {
				let profileVideo = document.querySelector('#profileVideo');
				if(profileVideo.paused)
					profileVideo.play();
				else
					profileVideo.pause();
			},
			logout() {
				User.logout({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token')).then((response) => {
					sessionStorage.removeItem('token');
					this.$router.push('loginForm');
				});
			},
			openSection(key) {
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
					case 'profile_video':
						if(this.getUserVideo())
							document.querySelector('#profileVideo').setAttribute('src', this.getUserVideo().profile_video);
						document.querySelector('#profileVideoForm').classList.toggle('d-none');
						break;
					case 'profile_image':
						if(this.getUserImage())
							document.querySelector('#profileImage').setAttribute('src', this.getUserImage().profile_image);
						document.querySelector('#profileImageForm').classList.toggle('d-none');
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