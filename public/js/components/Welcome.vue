<template>
    <div class="text-light">
		<div class="row mx-auto">
			<div class="col">
				
				<div class="row text-center mt-5 mb-5 pb-5 pt-5">
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
				
				<div class="row mt-5 pt-5">
					<div class="col">
					
						<div class="row mt-5">
							<div class="col">
								<button v-on:click="newGame" id="startButton" type="button" class="btn btn-dark active w-100">New Game</button>
							</div>
						</div>
						
						<div v-if="!!saveGame" class="row mt-5">
							<div class="col">
								<button v-on:click="loadGame" id="continueButton" type="button" class="btn btn-dark active w-100">Continue</button>
							</div>
						</div>
						
						<div class="row mt-5">
							<div class="col">
								<button v-on:click="listScores" type="button" class="btn btn-dark active w-100">Scores</button>
							</div>
						</div>
						
						<div class="row mt-5">
							<div class="col">
								<button v-on:click="chat" type="button" class="btn btn-dark active w-100">Chat</button>
							</div>
						</div>
						
						<div class="row mt-5">
							<div class="col">
								<button v-on:click="gameStore" type="button" class="btn btn-dark active w-100">Store</button>
							</div>
						</div>

					</div>		
				</div>	
				
				<nav class="row fixed-bottom">
					<div class="col btn-group" role="group">
						<button v-on:click="logout" type="button" class="btn btn-dark w-100">Logout</button>
					</div>				
				</nav>
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
			let responseData = this.$route.params.response.data;
			this.username = responseData.name;
			if(responseData.saveGame != null)
				this.saveGame = true;
		},
		methods: {
			loadGame(){
			
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
			logout() {
				User.logout({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token')).then((response) => {
					sessionStorage.removeItem('token');
					this.$router.push('loginForm');
				});
			}
		},
		computed: {
			navError (){
				return this.$route.params.navError;
			}
		}
	}
</script>
<style scoped>
</style>