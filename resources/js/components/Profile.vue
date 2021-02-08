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
											<span v-else class="pl-3">{{key}}</span>
										</div>
										<div class="col-2">
											<button v-bind:class="key" v-if="['avatar','name','email','credits','membership','profile_video'].indexOf(key) > -1" v-on:click="openSection(key)" class="float-right border border-success badge badge-pill badge-secondary align-middle">							
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
										<div v-else class="col">
											{{value}}
										</div>
									</div>
									<div v-if="key == 'avatar'" id="avatarForm" class="d-none row ml-0 mr-0 bg-info">
										<div class="col">
											avatar form
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
													<input class="mt-1 mb-1" type="text" name="confirmNewName" id="confirmNewName" placeholder="confirm new name"></input>
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
													<input class="mt-1 mb-1" type="text" name="confirmNewEmail" id="confirmNewEmail" placeholder="confirm new email"></input>
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
										
											<div class="row">
												<div class="col">
													<video id="profileVideo" width="420">
													<source src="" type="video/mp4">
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
													<input class="mt-1 mb-1" type="text" name="currentPassword" id="currentPassword" placeholder="enter current password"></input>
												</div>
											</div>
											<div class="row">
												<div class="col-10">
													<input class="mt-1 mb-1" type="text" name="newPassword" id="newPassword" placeholder="enter new password"></input>
												</div>
											</div>
											<div class="row">
												<div class="col-10">		
													<input class="mt-1 mb-1" type="text" name="confirmPassword" id="confirmPassword" placeholder="confirm new password"></input>
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
				formData: ''
			}
		},
		filters: {
			cleanLaravelDate: 
				function(value) {
					return value.substring(0,10);
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
			updateName() {
				
			},
			updateEmail() {
				
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
			updateProfileVideo() {
				console.log(this.profileVideo);
				this.formData = new FormData();
				this.formData.append('profileVideo', this.profileVideo);
				this.formData.append('_method', 'POST');
				
				for (var key of this.formData.entries()) {
				console.log(key[0] + ', ' + key[1]);
				}
				
				
				
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
  return response
})

return;
				
				
				Csrf.getCookie().then(() => {
					User.updateProfileVideo({
						_method: 'POST',
						profileVideo: this.formData,
						token: sessionStorage.getItem('token'),
					},
						sessionStorage.getItem('token')
					)
					.then(response => {
						console.log(response);
						this.errorList = [];
					})
					.catch(error => {
						if(error.response.status == 422)
							this.errorList = error.response.data.errors;
					});
				});
			},
			updatePassword() {
			
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
					case 'profile_video':
						document.querySelector('#profileVideoForm').classList.toggle('d-none');
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