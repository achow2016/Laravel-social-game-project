<template>
    <div class="container-fluid d-flex flex-column text-light">
	
		<header class="row fixed-top">
			<div class="col text-center d-flex">		
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
				</div>	
				<div class="flex-fill w-33 h-75">
					<h3 class="mt-1">Scores</h3>
				</div>	
				<div class="flex-fill w-33">
					<button v-on:click="logout" type="button" class="btn btn-dark flex-fill w-100">Logout</button>
				</div>	
			</div>
		</header>
	
		<div class="row mt-5 mb-2">
			<div id="messageContainer" class="col overflow-auto" style="white-space:pre;height:40px">
			</div>
		</div>
		
		<div class="row mt-5 mb-2">
			<div id="scoreContainer" class="col">
			
				<div class="row">
					<div class="col-4 text-center mb-2">User</div>
					<div class="col-4 text-center mb-2">Chara</div>
					<div class="col-4 text-center mb-2">Score</div>
				</div>
			
			</div>
		</div>
		
    </div>
</template>
<script>
	export default {
		props : [],
		data() {
			return {
				scoreData: ''
			}
		},
		beforeMount() {
		},
		mounted() {
			//dynamic style fix for small screen
			//remove large margins around map
			if(screen.height < 600) {
				document.getElementById('gridArea').classList.toggle('mb-5');
				document.getElementById('gridArea').classList.toggle('mt-1');
				
				let gridItems = document.getElementsByClassName('gameGridSquare');
				for(let i = 0; i < gridItems.length; i++) {
					gridItems[i].classList.toggle('pb-2');
					gridItems[i].classList.toggle('pt-2');
				}
				
				//set body font size to .8 rem
				document.getElementsByTagName('body')[0].style.fontSize = '.8rem';
			}

			if(screen.height > 800) {
				document.getElementsByTagName('body')[0].style.fontSize = '1.0rem';
			}
			
			this.formData = new FormData();
				this.formData.append('_method', 'POST');

			const headers = { 
			  'Content-Type': 'multipart/form-data',
			  'enctype' : 'multipart/form-data',
			  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
			}
			
			const getScores = async function(formData) {
				try {
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/getScores',
						params : '',
						data   : formData,
						headers: headers,
					});
					return response;
				}
				catch(err) {
					console.error(err);
				}
			};
			
			getScores(this.formData)
			.then(response => {
				console.log(response);		
				let characterScores = response.data.scores;
				if(characterScores.length == 0)
						document.getElementById('scoreContainer').textContent = 'Empty';
				else	
					for(let i = 0; i < characterScores.length; i++) {
						this.generateClickableScoreRow(characterScores[i]);
					}
			});
			
		},
		methods: {
			generateClickableScoreRow(score) {
				console.log(score);
				var vm = this;
				let scoreRowContainer = document.createElement('div');   
				scoreRowContainer.classList.add('row');
				
				let owner = document.createElement('div');   
				owner.classList.add('col-4', 'mb-2', 'text-center');
				owner.textContent = score.userName;
				owner.setAttribute('id', score.userName);
				owner.setAttribute('style', 'text-decoration: underline');
				owner.addEventListener('click', function(event) {
					//vm.expandItem(event.target.id);
				});
				scoreRowContainer.appendChild(owner);
				
				let characterName = document.createElement('div');   
				characterName.classList.add('col-4', 'mb-2', 'text-center');
				characterName.textContent = score.characterName;
				scoreRowContainer.appendChild(characterName);
				
				let scoreAmount = document.createElement('div');   
				scoreAmount.classList.add('col-4', 'mb-2', 'text-center');
				scoreAmount.textContent = score.score;
				scoreRowContainer.appendChild(scoreAmount);
				
				
				document.getElementById('scoreContainer').appendChild(scoreRowContainer);
			},
			logout() {
				User.logout({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					sessionStorage.removeItem('token');
					this.$router.push('loginForm');
				});
			},
		}
	}
</script>

<style scoped>
</style>