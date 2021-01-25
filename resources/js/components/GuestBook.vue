<template>
    <div class="container-fluid d-flex flex-column text-light">
		<div class="row mx-auto">
			<div class="col">
				
				<header class="row fixed-top">
					<div class="col text-center d-flex">		
						<div class="flex-fill w-100">
							<span>Guestbook</span>
						</div>	
					</div>
				</header>
				
				<div v-if="errorList.ipError" class="col-sm-8 alert alert-warning text-center" role="alert">
					<span class="text-danger">{{errorList.ipError[0]}}</span>
				</div>
			
				<div class="row text-center mt-5 t-5">
					<div class="col">
						<h1>My Website</h1>
						<h5>Welcome, {{guestName}}!</h5>
					</div>
				</div>
				
				<div class="row text-center">
					<div class="col">
						<h5>Visitor: {{guestCount}}</h5>
					</div>
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
											Date: Date.parse({{guestbookNote.date}})
										</div>
									</div>
								
								</div>
							</div>
						</div>
					</div>
				</section>

				
				<div class="row fixed-bottom">
					<div class="col pr-0">
						<router-link :to="{ name: 'home' }"><button type="button" class="btn btn-dark w-100">Home</button></router-link>
					</div>	
					
					<div class="col pl-0">
						<button v-on:click="signGuestbook" id="signGuestbook" type="button" class="btn btn-dark w-100">Sign Guestbook</button>
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
				guestname: '',
				guestCount: '',
				guestbookNotes: ''
			}
		},
		created() {
			let responseData = this.$route.params.response.data;
			this.guestName = responseData.guestName;
			this.guestCount = responseData.guestCount;
			if(this.$route.params.response.data.error) {
				this.errorList = {'ipError' : []};
				this.errorList.ipError[0] = this.$route.params.response.data.error;
			}	
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
			signGuestbook() {
			
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