<template>
    <div class="container-fluid d-flex flex-column text-light">
	
		<header class="row fixed-top">
			<div class="col text-center d-flex">		
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
				</div>	
				<div class="flex-fill w-33 h-75">
					<h3 class="mt-1">Rpg Game Battle</h3>
				</div>	
				<div class="flex-fill w-33">
					<button v-on:click="logout" type="button" class="btn btn-dark flex-fill w-100">Logout</button>
				</div>	
			</div>
		</header>
		
		<div class="row mt-5 mb-5" id="battleSceneArea">
			<div class="col">		
				<div>
					<div id="battleScene" class="col text-center">
						Generating battle scene...
					</div>					
				</div>
			</div>
		</div>
		
		<div class="row mt-5 mb-5" id="distanceGridArea">
			<div class="col">		
				<div>
					<div id="distanceGrid" class="col text-center">
						Generating distance grid...
					</div>					
				</div>
			</div>
		</div>
		
		<div class="row text-center mt-5 mb-2">
			<div id="messageContainer" class="col text-center">
				Messages
			</div>
		</div>
		
		<div class="row mt-5 mb-5" id="controlArea">
			<div id="actionGrid" class="col-6">
				<div v-on:click="toggleInspectMenu" class="row-9 mb-4 actionRow d-flex justify-content-center">Inspect</div>
				<div class="row-9 mb-4 actionRow d-flex justify-content-center">Fight</div>
				<div class="row-9 mb-4 actionRow d-flex justify-content-center">Skill</div>
				<div class="row-9 mb-4 actionRow d-flex justify-content-center">Flee</div>
			</div>
		</div>
		
		<div class="row mt-5 mb-5">
			<div class="col overflow-auto text-center" id="menuDataArea">
				loading data...
			</div>
		</div>
		
		<div class="row fixed-bottom">
		
			<div id="bottomMenuBar" class="col text-center d-flex">		
				<div class="flex-fill w-33">
					<button v-on:click="toggleInventory" type="button" class="btn btn-dark flex-fill w-100 pl-0 pr-0">Inventory</button>
				</div>	
				<div class="flex-fill w-33 h-75">
					<button v-on:click="toggleStatus" type="button" class="btn btn-dark flex-fill w-100">Status</button>
				</div>	
				<div class="flex-fill w-33">
					<button v-on:click="toggleGameMenu" type="button" class="btn btn-dark flex-fill w-100">Menu</button>
				</div>
			</div>
			
			<div id="currentMenuControl" class="col text-center d-none">
				<div id="closeInventoryContainer" class="flex-fill w-100 d-none">
					<button v-on:click="toggleInventory" type="button" class="btn btn-dark flex-fill w-100">Close Inventory</button>
				</div>	
				<div id="closeStatusContainer" class="flex-fill w-100 d-none">
					<button v-on:click="toggleStatus" type="button" class="btn btn-dark flex-fill w-100">Close Status</button>
				</div>
				<div id="closeInspectContainer" class="flex-fill w-100 d-none">
					<button v-on:click="toggleInspectMenu" type="button" class="btn btn-dark flex-fill w-100">Close Inspect</button>
				</div>				
				<div id="closeGameMenuContainer" class="flex-fill w-100 d-none">
					<button v-on:click="toggleGameMenu" type="button" class="btn btn-dark flex-fill w-100">Close Menu</button>
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
				mapData: '',
				playerPosition: '',
				lastPlayerPosition: '',
				terrainLayerData: '',
				playerStatus: '',
				enemyData: '',
				enemyStatusData: ''
			}
		},
		beforeMount() { 
			User.getMap({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					this.mapData = JSON.parse(response.data.mapData);
					this.playerPosition = response.data.playerPosition;
					
					document.getElementById('mapGrid').innerHTML = ""; 
					for (let i = 0; i < 8; i++) {
						let row = document.createElement('div');
						row.classList.add('row', 'mapGridRow');
						row.setAttribute('id', 'row' + i);
						document.getElementById('mapGrid').appendChild(row);
						
						for (let j = 0; j < 8; j++) {
							let element = document.createElement('div');
							element.classList.add('col');
							//element.setAttribute('id', 'row' + i + 'col' + j);
							
							if(this.mapData[i][j].terrain == 'grass')
								element.classList.add('gameGridSquare', 'bg-success', 'pt-2', 'pb-2', 'border', 'border-dark');
							else
								element.classList.add('gameGridSquare', 'bg-primary', 'pt-2', 'pb-2', 'border', 'border-dark');
							
							if(this.mapData[i][j].treeCover == true) {
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
					this.drawEnemyPositions();
				});
			
				
		},
		mounted() {
			//dynamic style fix for small screen
			//remove large margins around map
			if(screen.height < 600) {
				document.getElementById('gridArea').classList.toggle('mb-5');
				document.getElementById('gridArea').classList.toggle('mt-5');
				
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
		},
		methods: {
			drawPlayerPosition() {
				//store, get current coords
				this.lastPlayerPosition = this.playerPosition;
				let row = this.playerPosition[0];
				let column = this.playerPosition[1];
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
				User.getEnemies({
					_method: 'POST', token: sessionStorage.getItem('token')
					}, 
						sessionStorage.getItem('token')
					)
					.then((response) => {
						this.enemyData = response.data.enemies;
						for(let i = 0; i < this.enemyData.length; i++) {					
							//get current coords
							let row = this.enemyData[i][0];
							//let row = this.enemyData[i].mapPosition[0];
							let column = this.enemyData[i][1];
							//let column = this.enemyData[i].mapPosition[1];
							let enemySquare = document.getElementById(row + '-' + column);
							
							//outlines enemy square 
							if(!enemySquare.classList.contains('border-danger')) {
								enemySquare.classList.toggle('border-dark');
								enemySquare.classList.toggle('border-danger');
							}
							
							//remembers what was on the square so player icon can be drawn over it
							//this.terrainLayerData = playerSquare.textContent;
							
							//draws enemy onto square
							enemySquare.innerHTML = '';
							let enemyIcon = document.createElement('img');   
							enemyIcon.setAttribute('src', 'http://127.0.0.1:8000/img/bishop.svg');   
							enemyIcon.classList.toggle('img-fluid');   
							enemySquare.appendChild(enemyIcon);
						}
					})
					.catch(error => {
					//server response errors
					if (error.response) {
						console.log(error.response.data.message);
						document.getElementById('messageContainer').textContent = error.response.data.message;
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
			clearPlayerPosition() {
				//get current coords
				let row = this.lastPlayerPosition[0];
				let column = this.lastPlayerPosition[1];
				let playerSquare = document.getElementById(row + '-' + column);
				
				//reverses outline of player square
				playerSquare.classList.add('border-dark');
				playerSquare.classList.remove('border-warning');
				
				//draws terrain data back onto square
				playerSquare.innerHTML = '';
				playerSquare.innerHTML = this.terrainLayerData;
			},
			moveCharacter(event) {
				//document.getElementById('directionGrid').classList.toggle('d-none');
				let controllerArray = document.getElementsByClassName('controllerRow');
				for(let i = 0; i < controllerArray.length; i++) {
					controllerArray[i].classList.toggle('d-none');
				}
				document.getElementById('directionplaceholder').classList.toggle('d-none');
			
				this.formData = new FormData();
				this.formData.append('direction', event.currentTarget.id);
				this.formData.append('_method', 'POST');
	
				const headers = { 
				  'Content-Type': 'multipart/form-data',
				  'enctype' : 'multipart/form-data',
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				}
				axios({
					method : "POST",
					baseURL: 'http://127.0.0.1:8000/api',
					url    : 'http://127.0.0.1:8000/api/moveCharacter',
					params : '',
					data   : this.formData,
					headers: headers,
				}).then(response => {
					console.log(response);
					if(response.data.message)
						document.getElementById('messageContainer').textContent = response.data.message;
					
					this.playerPosition = response.data.playerPosition;
					this.clearPlayerPosition();
					this.drawPlayerPosition();
					//document.getElementById('directionGrid').classList.toggle('d-none');
					let controllerArray = document.getElementsByClassName('controllerRow');
					for(let i = 0; i < controllerArray.length; i++) {
						controllerArray[i].classList.toggle('d-none');
					}
					document.getElementById('directionplaceholder').classList.toggle('d-none');
				})
				
				this.drawEnemyPositions();
			},
			toggleInventory() {
				//closes map controls area
				document.getElementById('controlArea').classList.toggle('d-none');
				
				//closes bottom game menu bar
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				//shows target meny button
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				document.getElementById('closeInventoryContainer').classList.toggle('d-none');
			},
			toggleStatus() {
				//gets status data only when toggle to make status container visible
				if(document.getElementById('closeStatusContainer').classList.contains('d-none'))
					this.populateStatus();
				else
					document.getElementById('menuDataArea').textContent = 'loading data...';
					
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				document.getElementById('closeStatusContainer').classList.toggle('d-none');
			},
			toggleGameMenu() {
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeGameMenuContainer').classList.toggle('d-none');
			
			},
			toggleInspectMenu() {
				//gets status data only when toggle to make status container visible
				if(document.getElementById('closeInspectContainer').classList.contains('d-none'))
					this.populateInspect();
				else
					document.getElementById('menuDataArea').textContent = 'loading data...';
					
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeInspectContainer').classList.toggle('d-none');
				
			},			
			populateInspect() {
				User.inspectEnemies({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					console.log(response.data.enemies);
					this.enemyStatusData = response.data.enemies;
					document.getElementById('menuDataArea').textContent = '';
					
					for(let i = 0; i < this.enemyStatusData.length; i++) {
						this.generateDataRow('Name', this.enemyStatusData[i].name);
						this.generateDataRow('Direction', this.enemyStatusData[i].mapOrientation);
						this.generateDataRow('Attack', this.enemyStatusData[i].currentAttack + '/' + this.enemyStatusData[i].attack);
						this.generateDataRow('Health', this.enemyStatusData[i].currentHealth + '/' + this.enemyStatusData[i].health);
						this.generateDataRow('Stamina', this.enemyStatusData[i].currentStamina + '/' + this.enemyStatusData[i].stamina);
						//this.generateDataRow('Recovery', 'H: ' + this.enemyStatusData[i].currentHealthRegen + '/' + //this.enemyStatusData[i].healthRegen
						//	+ ' | ' + 'S: ' + this.enemyStatusData[i].currentstaminaRegen + '/' + this.enemyStatusData[i].staminaRegen);
						//this.generateDataRow('Agility', this.enemyStatusData[i].currentAgility + '/' + this.enemyStatusData[i].agility);
						//this.generateDataRow('Accuracy', this.enemyStatusData[i].currentAccuracy + '/' + this.enemyStatusData[i].accuracy);
						//this.generateDataRow('money', this.enemyStatusData[i].money);
					}
				})
				.catch(error => {
					//server response errors
					if (error.response) {
						console.log(error.response.data.message);
						document.getElementById('messageContainer').textContent = error.response.data.message;
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
			saveAndQuit() {

			},
			generateDataRow(key, data) {
				let dataRowContainer = document.createElement('div');   
				dataRowContainer.classList.add('row');
				
				let dataRowFieldKey = document.createElement('div');   
				dataRowFieldKey.classList.add('col-6', 'text-center');
				dataRowFieldKey.textContent = key;
				dataRowContainer.appendChild(dataRowFieldKey);
				
				let dataRowFieldData = document.createElement('div'); 
				dataRowFieldData.classList.add('col-6', 'text-center');
				dataRowFieldData.textContent = data;
				dataRowContainer.appendChild(dataRowFieldData);
				
				document.getElementById('menuDataArea').appendChild(dataRowContainer);
			},
			populateStatus() {
			/*
				User.getCharacterStatus({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					console.log(response.data.character);
					this.playerStatus = response.data.character;
					document.getElementById('menuDataArea').textContent = '';
					
					this.generateDataRow('Game Level', response.data.character.gameLevel);
					this.generateDataRow('Name', response.data.character.characterName);
					this.generateDataRow('Attack', response.data.character.currentAttack + '/' + response.data.character.attack);
					this.generateDataRow('Health', response.data.character.currentHealth + '/' + response.data.character.health);
					this.generateDataRow('Stamina', response.data.character.currentStamina + '/' + response.data.character.stamina);
					this.generateDataRow('Recovery', 'H: ' + response.data.character.currentHealthRegen + '/' + response.data.character.healthRegen
						+ ' | ' + 'S: ' + response.data.character.currentstaminaRegen + '/' + response.data.character.staminaRegen);
					this.generateDataRow('Agility', response.data.character.currentAgility + '/' + response.data.character.agility);
					this.generateDataRow('Accuracy', response.data.character.currentAccuracy + '/' + response.data.character.accuracy);
					this.generateDataRow('money', response.data.character.money);
				});
			*/	
				this.formData = new FormData();
				this.formData.append('token', sessionStorage.getItem('token'));
				this.formData.append('_method', 'POST');
	
				const headers = { 
				  'Content-Type': 'application/json',
				  'enctype' : 'application/x-www-form-urlencoded',
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				}
				axios({
					method : "POST",
					baseURL: 'http://127.0.0.1:8000/api',
					url    : 'http://127.0.0.1:8000/api/getCharacterStatus',
					params : '',
					data   : this.formData,
					headers: headers,
				}).then(response => {
					this.playerStatus = response.data.character;
					document.getElementById('menuDataArea').textContent = '';
					
					this.generateDataRow('Name', response.data.character.characterName);
					this.generateDataRow('Health', response.data.character.currentHealth + '/' + response.data.character.health);
					this.generateDataRow('Stamina', response.data.character.currentStamina + '/' + response.data.character.stamina);
					this.generateDataRow('Recovery', 'H ' + response.data.character.healthRegen + ' / ' + 'S ' + response.data.character.staminaRegen);
					this.generateDataRow('Agility', response.data.character.agility);
					this.generateDataRow('Accuracy', response.data.character.accuracy);
					this.generateDataRow('money', response.data.character.money);					
				}).catch(error => {
					console.log(error)
				})
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
	.actionRow {
		border: 1px solid white;
	}

</style>