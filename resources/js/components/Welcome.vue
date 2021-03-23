<template>
    <div class="text-light">
		<div class="row mx-auto">
			<div class="col">
				
				<div class="row text-center mt-5 mb-5">
					<div class="col">
						<h1>Rpg game</h1>
						<h5>Welcome, {{username}}!</h5>
					</div>
				</div>
				
				<div v-if="!!navError" class="row text-center">
					<div class="col-sm-8 alert alert-warning" role="alert">
						<span class="text-danger">{{navError}}</span>
					</div>
				</div>
				
				<div v-if="!!errorMessage" class="row text-center">
					<div class="col-sm-8 alert alert-warning" role="alert">
						<span class="text-danger">{{errorMessage}}</span>
					</div>
				</div>
				
				<div class="row mt-5 pt-5">
					<div class="col">
					
						<div class="row mt-5 menuOption">
							<div class="col">
								<button v-on:click="newGame" id="startButton" type="button" class="btn btn-dark active w-100">New Game</button>
							</div>
						</div>
						
						<div v-if="!!saveGame" class="row mt-5 menuOption">
							<div class="col">
								<button v-on:click="continueGame" id="continueButton" type="button" class="btn btn-dark active w-100">Continue</button>
							</div>
						</div>
						
						<div class="row mt-5 menuOption">
							<div class="col">
								<button v-on:click="listScores" type="button" class="btn btn-dark active w-100">Scores</button>
							</div>
						</div>
						
						<div class="row mt-5 menuOption">
							<div class="col">
								<button v-on:click="chat" type="button" class="btn btn-dark active w-100">Chat</button>
							</div>
						</div>
						
						<div class="row mt-5 menuOption">
							<div class="col">
								<button v-on:click="gameStore" type="button" class="btn btn-dark active w-100">Store</button>
							</div>
						</div>
						
						<div class="row mt-5 menuOption">
							<div class="col">
								<button v-on:click="profile" type="button" class="btn btn-dark active w-100">Profile</button>
							</div>
						</div>
						
						<div class="row mt-5 menuOption">
							<div class="col">
								<button v-on:click="logout" type="button" class="btn btn-dark w-100">Logout</button>
							</div>
						</div>

					</div>		
				</div>	
				
			</div>
		</div>
    </div>
</template>
<script>
	import User from '../apis/User';
	export default {
		props : ['title','message','error'],
		data() {
			return {
				username: '',
				saveGame: ''
			}
		},
		created() {
			console.log(this.$route.params.errorMessage);
			let responseData = this.$route.params.response.data;
			this.username = responseData.user.name;
			if(responseData.characterState == 'true')
				this.saveGame = true;
			console.log(responseData);
		},
		methods: {
			continueGame(){
				this.$router.push('rpgGame').catch(() => {})
			},
			newGame() {
				this.$router.push('characterBuilder')
			},
			listScores() {
				this.$router.push('listScores')
			},
			chat() {
				this.$router.push('chat')
			},
			gameStore() {
				this.$router.push('store')
			},
			profile() {
				this.$router.push('profile')
			},
			logout() {
				User.logout({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token')).then((response) => {
					sessionStorage.removeItem('token');
					this.$router.push('loginForm');
				});
			}
		},
		computed: {
			navError() {
				return this.$route.params.navError;
			},
			
			errorMessage() {
				//return this.$route.params.navError;
				
				return this.$route.params.errorMessage;
			}
		}
	}
</script>
<style scoped>
	
	@media (min-width: 406px) and (max-width: 767.98px) and (orientation: landscape){ 
		.row .mt-5 .menuOption {
			width: 80% !important;
			margin: auto !important;
		}	
	}

	@media (min-width: 400px) {
		.row .mt-5 .menuOption {
			width: 60% !important;
			margin: auto !important;
			padding-top: 20px !important;
		}	
	}

</style>