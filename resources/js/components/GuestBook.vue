<template>
    <div class="container-fluid d-flex flex-column text-light">
		<div class="row mx-auto">
			<div class="col">
				
				<header class="row">
					<div class="col text-center d-flex">		
						<div class="flex-fill w-100">
							<h1>Guest Book</h1>
						</div>	
					</div>
				</header>
				
				<div class="row text-center mt-5 t-5">
					<div class="col">
						<h5>Welcome, {{guestName}}!</h5>
					</div>
				</div>
				
				<div class="row text-center">
					<div class="col">
						<h5>Visitors: {{guestCount}}</h5>
					</div>
				</div>
				
				<div v-if="errorList.duplicateError" class="col-sm-8 alert alert-warning" role="alert">
					<span class="text-danger">{{errorList.duplicateError[0]}}</span>
				</div>
				
				<div v-if="errorList.dbError" class="col-sm-8 alert alert-warning" role="alert">
					<span class="text-danger">{{errorList.dbError[0]}}</span>
				</div>
				
				<div v-if="errorList.inputError" class="col-sm-8 alert alert-warning" role="alert">
					<span class="text-danger">{{errorList.inputError[0]}}</span>
				</div>
				
				<section class="row text-center mt-5 mb-2">
					<div class="col" id="entryContainer">
						<div v-if="!!guestbookNotes" class="col-sm-12">
							<div v-for="guestbookNote in guestbookNotes" class="row bg-secondary mb-3">
								<div class="float-none w-100">
									<div class="row">
										<div class="col">
											Name: {{guestbookNote.name}}
										</div>
									</div>
								
									<div class="row">
										<div class="col">
											Email: {{guestbookNote.email}}
										</div>
									</div>
								
									<div class="row">
										<div class="col">
											Message: {{guestbookNote.note}}
										</div>
									</div>
									
									<div class="row">
										<div class="col">
											Date: {{guestbookNote.date}}
										</div>
									</div>
								
								</div>
							</div>
						</div>
					</div>
				</section>

				
				<section id="postBox" class="row text-center mt-2">
					<div id="postContainer" class="fixed-bottom col text-center mt-2 mb-5 pt-2 pb-2 d-none">
						<div class="col bg-light pt-2 pb-2">
							<div class="row">
								<div class="col">
									<form>
										<div v-if="errorList.name" class="col-sm-8 alert alert-warning" role="alert">
											<span class="text-danger">{{errorList.name[0]}}</span>
										</div>
										<div v-if="errorList.text" class="col-sm-8 alert alert-warning" role="alert">
											<span class="text-danger">{{errorList.text[0]}}</span>
										</div>											
										<input type="text" v-model="name" class="form-control" id="name" placeholder="name (required)">
										<input type="email" v-model="email" class="form-control" id="email" placeholder="email">
										<textarea id="postSpace" v-model="text" rows="4" class="w-100" placeholder="message (required)"></textarea">
									</form>
								</div>
							</div>
							<div class="row d-flex flex-row-reverse mr-1">
								<button type="submit" @click.prevent="submitGuestbookNote" class="btn btn-dark w-25">Sign</button>
								<button v-on:click="toggleSignGuestbook()" type="button" class="btn btn-dark w-25">Close</button>
							</div>
						</div>
					</div>	
				</section>
				
				
				<div class="row fixed-bottom">
					<div class="col pr-0">
						<router-link :to="{ name: 'home' }"><button type="button" class="btn btn-dark w-100">Home</button></router-link>
					</div>	
					
					<div class="col pl-0">
						<button v-on:click="toggleSignGuestbook" id="startSignGuestbook" type="button" class="btn btn-dark w-100">Sign Guestbook</button>
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
		props : ['title','message','error'],
		data() {
			return {
				errorList: [],
				guestName: '',
				guestCount: '',
				guestbookNotes: '',
				name: '',
				email: '',
				text: ''
			}
		},
		created() {
			let responseData = this.$route.params.response.data;
			this.guestName = responseData.guestName;
			this.guestCount = responseData.guestCount;
		},
		updated: function() {
			let guestbookPosts = document.getElementsByClassName('bg-secondary');
			let count = guestbookPosts.length;
			if(count >= 1)
				document.getElementsByClassName('bg-secondary')[count - 1].classList = 'row bg-secondary mb-5';
		}, 
		mounted: function() {
			User.getGuestbookNotes({
						_method: 'GET',
					})
					.then(response => {
						this.guestbookNotes = response.data.guestBookNotes;
						console.log(response.data);
					})
					.catch(error => {
						console.log(error);
					});
					
		},
		methods: {
			getGuestbookNotes() {
				User.getGuestbookNotes({
						_method: 'GET'
					})
					.then(response => {
						this.guestbookNotes = response.data.guestBookNotes;
						console.log(response.data);
					})
					.catch(error => {
						console.log(error);
					});
			},
			toggleSignGuestbook() {
				document.getElementById('postContainer').classList.toggle('d-none');
				
			},
			submitGuestbookNote() {
				if(this.name == '') {
					this.errorList = {'name' : []};
					this.errorList.name[0] = 'no name!';
					return;
				}
				if(this.text == '') {
					this.errorList = {'text' : []};
					this.errorList.text[0] = 'no text!';
					return;
				}	
				
				User.addGuestbookNote({
					_method: 'POST',
					note: this.text,
					name: this.name,
					email: this.email
				})
				.then(response => {
					this.getGuestbookNotes();
					postSpace.value = '';
					this.errorList = [];
					document.getElementById('name').value = '';
					document.getElementById('email').value = '';
					document.getElementById('postSpace').value = '';
					this.toggleSignGuestbook();
				})
				.catch(error => {
					if(error.response.status == 422 && error.response.data.duplicateError != null) {
						this.errorList = {'duplicateError' : []};
						this.errorList.duplicateError[0] = error.response.data.duplicateError;
					}
					
					if(error.response.status == 422 && error.response.data.dbError != null) {
						this.errorList = {'dbError' : []};
						this.errorList.dbError[0] = error.response.data.dbError;
					}
					
					if(error.response.status == 422 && error.response.data.inputError != null) {
						this.errorList = {'inputError' : []};
						this.errorList.inputError[0] = error.response.data.inputError;
					}
					
					console.log(error);
					document.getElementById('name').value = '';
					document.getElementById('email').value = '';
					document.getElementById('postSpace').value = '';
					this.toggleSignGuestbook();
				});
			}
		},
		computed: {
			sysError() {
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