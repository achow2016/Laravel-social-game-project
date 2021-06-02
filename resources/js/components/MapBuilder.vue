<template>
    <div class="container-fluid d-flex flex-column text-light">
	
		<header class="row fixed-top">
			<div class="col text-center d-flex">		
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
				</div>	
				<div class="flex-fill w-33 h-75">
					<h3 class="mt-1">Map Builder</h3>
				</div>	
				<div class="flex-fill w-33">
					<button v-on:click="logout" type="button" class="btn btn-dark flex-fill w-100">Logout</button>
				</div>	
			</div>
		</header>
	
		<div class="row text-center mt-5 mb-2">
			<div class="col">
				<h5>Rpg game map builder</h5>
			</div>
		</div>
		
		<div class="row mt-5 mb-5">
			<div class="col">		
				<div>
					<div id="mapGrid" class="col text-center">
						Generating map.
					</div>					
				</div>
			</div>
		</div>
		
		<div class="row mt-2 mb-2">
			<div class="col">		
				
				<div class="row">
					<div id="textNotifications" class="col text-center">
					</div>
				</div>
				
			</div>
		</div>
		
		<div class="row fixed-bottom">
			<div class="col">
				<div class="centered-button">
					<button v-on:click="beginGame" id="beginGame" type="button" class="w-100 btn btn-dark active">Begin Game</button>
				</div>
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
				startPoint: '',
				mapData: '',
				playerPosition: '',
				enemyData: ''
			}
		},
		beforeMount() { 
			User.generateMap({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					let gameMap = response.data.gameMap;
					let mapData = response.data.mapData;
					let tileSet = response.data.tileSet;
					let gameLevel = response.data.gameLevel + '.\r\n';
					let message = response.data.message + '\r\n';
					
					this.startPoint = [gameMap.startPoint[0], gameMap.startPoint[1]];
					this.mapData = mapData;
					
					let levelText = 'Welcome to level ' + gameLevel;
					document.getElementById('textNotifications').innerText = levelText;
					
					if(!localStorage.hasOwnProperty('gameLog'))
						localStorage.setItem('gameLog', levelText);
					else
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + levelText);
		
		
					//prints message if attempted generating map when game in session
					if(message != null) {
						document.getElementById('textNotifications').innerText += message;
					
						if(!localStorage.hasOwnProperty('gameLog'))
							localStorage.setItem('gameLog', message);
						else
							localStorage.setItem('gameLog', localStorage.getItem('gameLog') + message);
					
					}
		
		
					document.getElementById('mapGrid').innerHTML = ""; 
					for (let i = 0; i < 8; i++) {
						let row = document.createElement('div');
						row.classList.add('row');
						row.setAttribute('id', 'row' + i);
						document.getElementById('mapGrid').appendChild(row);
						
						for (let j = 0; j < 8; j++) {
							let element = document.createElement('div');
							element.classList.add('col');
							//element.setAttribute('id', 'row' + i + 'col' + j);
							
							if(mapData[i][j].terrain == 'grass')
								element.classList.add('bg-success', 'pt-2', 'pb-2', 'border', 'border-dark');
							else
								element.classList.add('bg-primary', 'pt-2', 'pb-2', 'border', 'border-dark');
							
							if(mapData[i][j].treeCover == true) {
								let treeMarker = document.createTextNode('T');
								element.id = i + '-' + j;
								element.appendChild(treeMarker);
								element.classList.add('tree');
							}
							else {
								let openMarker = document.createTextNode('-');
								element.id = i + '-' + j;
								element.appendChild(openMarker);
								element.classList.add('open');
							}
							document.getElementById('row' + i).appendChild(element);
						}
					}
					
					this.drawPlayerPosition();
					
					User.generateEnemies({
					_method: 'POST', token: sessionStorage.getItem('token')
					}, 
						sessionStorage.getItem('token')
					)
					.then((response) => {
						let message = response.data.message + '\r\n';
					
						//prints message if attempted generating enemies onto game in session
						if(message != null) {
							document.getElementById('textNotifications').innerText += message;
						
							if(!localStorage.hasOwnProperty('gameLog'))
								localStorage.setItem('gameLog', message);
							else
								localStorage.setItem('gameLog', localStorage.getItem('gameLog') + message);
								
						}
						this.enemyData = response.data.enemies;
						this.drawEnemyPositions();
					});	
				}).catch(error => {
					//server response errors
					if (error.response) {
						console.log(error.response.data.message);
						document.getElementById('mapGrid').textContent = error.response.data.message;
						document.getElementById('beginGame').remove();
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
		mounted() {
		},
		methods: {
			beginGame() {
				this.$router.push('rpgGame');
			},
			drawPlayerPosition() {
				//get current coords
				let row = this.startPoint[0];
				let column = this.startPoint[1];
				let playerSquare = document.getElementById(row + '-' + column);
				
				//outlines player square
				playerSquare.classList.toggle('border-dark');
				playerSquare.classList.toggle('border-warning');
				
				//remembers what was on the square so player icon can be drawn over it
				this.terrainLayerData = playerSquare.textContent;
				
				//draws player onto square
				playerSquare.innerHTML = '';
				let playerIcon = document.createElement('img');   
				playerIcon.setAttribute('src', 'http://127.0.0.1:8000/img/pawn.svg');   
				playerIcon.classList.toggle('img-fluid');   
				playerSquare.appendChild(playerIcon);
			},
			drawEnemyPositions() {
				for(let i = 0; i < this.enemyData.length; i++) {					
					//get current coords
					//let row = this.enemyData[i].mapPosition[0];
					let row = this.enemyData[i][0];
					//let column = this.enemyData[i].mapPosition[1];
					let column = this.enemyData[i][1];
					let enemySquare = document.getElementById(row + '-' + column);
					
					//outlines player square
					enemySquare.classList.toggle('border-dark');
					enemySquare.classList.toggle('border-danger');
					
					//remembers what was on the square so player icon can be drawn over it
					//this.terrainLayerData = playerSquare.textContent;
					
					//draws player onto square
					enemySquare.innerHTML = '';
					let enemyIcon = document.createElement('img');   
					enemyIcon.setAttribute('src', 'http://127.0.0.1:8000/img/bishop.svg');   
					enemyIcon.classList.toggle('img-fluid');   
					enemySquare.appendChild(enemyIcon);
				}
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