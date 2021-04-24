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
			<div id="battleScenePlayer" class="col-6 bg-secondary text-center">
				Generating player avatar...
			</div>
			<div id="battleSceneEnemy" class="col-6 bg-secondary text-center">
				Generating enemy avatar...
			</div>
		</div>
		
		<div class="row mt-5 mb-5" id="distanceGridArea">
			<div class="col">		
				<div>
					<div class="col text-center">
						<div id="distanceGrid" class="row justify-content-center">
							Generating distance grid...
						</div>
					</div>					
				</div>
			</div>
		</div>
		
		<div class="row text-center mt-5 mb-2">
			<div id="messageContainer" class="col text-center bg-secondary">
				Messages
			</div>
		</div>
		
		<div class="row mt-5 controlArea bg-secondary">
			<div id="playerInfo" class="col-6">
			</div>
			<div id="enemyInfo" class="col-6">
			</div>
		</div>
		
		<div class="playerControls row mt-5 mb-5 controlArea bg-secondary">
			<div id="actionGrid" class="col">
				<div v-on:click="toggleInspectMenu" class="row-6 mt-3 mb-3 actionRow d-flex justify-content-center">Inspect</div>
				<div v-on:click="meleeEnemy" class="row-6 mt-3 mb-3 actionRow d-flex justify-content-center">Fight</div>
				<div class="row-6 mt-3 mb-3 actionRow d-flex justify-content-center">Skill</div>
				<div class="row-6 mt-3 mb-3 actionRow d-flex justify-content-center">Flee</div>
			</div>
		</div>
		
		<div class="row mt-5 mb-5">
			<div class="col overflow-auto text-center d-none bg-secondary" id="menuDataArea">
				loading data...
			</div>
		</div>
		
		<div class="playerControls row fixed-bottom">
		
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
		
		<div id="mapReturn" class="row fixed-bottom d-none">
			<div id="bottomMenuBar" class="col text-center d-flex">			
				<div class="flex-fill w-33 h-75">
					<button style="pointer-events:none" id="mapReturnButton" v-on:click="returnToMap" type="button" class="btn btn-dark flex-fill w-100">Return To Map</button>
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
				enemyStatusData: '',
				engageDistance: '',
				playerData: '',
			}
		},
		beforeMount() { 
			
		},
		mounted() {
			const vm = this;
			
			if(this.$route.params === null) {
				this.$router.push({ 
					name: 'rpgGame', 
					params: {message: 'No active battle found.'} 
				}).catch((err) => {
					console.log(err);
				});
			}
			else {
				//refresh page fix
				if(this.$route.params.enemy == null || this.$route.params.distance == null || this.$route.params.player == null) {
				
					let test = User.getData({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token'))
					.then((response) => {
						return true;
					})
					.catch(error => {
						if(error.response.status == 401) {
							this.$router.push({ 
								name: 'login', 
								params: {navError: 'You must be logged in to access that resource.'} 
							}).catch((err) => {
								console.log(err);
							});
						}
						else
							this.$router.push({ 
								name: 'login', 
								params: {navError: 'Could not get user state from database, please create an account or contact admin.'} 
							}).catch((err) => {
								console.log(err);
							});
					});
					
					if(test) {
						let characterExistenceStatus = User.getCharacterExistenceStatus({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token'))
						.then((response) => {
							if(response.data.characterStatus == false) {
								return false;
							}
							else
								return true;
						})
						.catch(error => {
							if(error.response.status == 401)
								this.$router.push({ 
									name: 'login', 
									params: {navError: 'You must be logged in to access that resource.'} 
								}).catch((err) => {
									console.log(err);
								});
							else if(error.response.status == 422)
								this.$router.push({ 
									name: 'login', 
									params: {navError: 'Could not get character state from database, please create a character or contact admin.'} 
								}).catch((err) => {
									console.log(err);
								});
							else
								this.$router.push({ 
									name: 'login', 
									params: {navError: 'Could not get user state from database, please create an account or contact admin.'} 
								}).catch((err) => {
									console.log(err);
								});
						});
						characterExistenceStatus.then(function(result) {
							if(result === false) {
								this.$router.push({ 
									name: 'welcome', 
									params: {errorMessage: 'You do not have an active character.'} 
								}).catch((err) => {
									console.log(err);
								});
							}
						});
						
						
						let battleStatusCheck = User.getBattleStatus({_method: 'POST', token: sessionStorage.getItem('token')}, sessionStorage.getItem('token'))
						.then((response) => {
							if(response.data.battleStatus == false) {
								return null;
							}
							else {
								return {'player': response.data.player, 'enemy': response.data.enemy, 'distance': response.data.distance};
							}	
						})
						.catch(error => {
							if(error.response.status == 401)
								this.$router.push({ 
									name: 'login', 
									params: {navError: 'You must be logged in to access that resource.'} 
								}).catch((err) => {
									console.log(err);
								});
							else if(error.response.status == 422)
								this.$router.push({ 
									name: 'login', 
									params: {navError: 'Could not get character state from database, please create a character or contact admin.'} 
								}).catch((err) => {
									console.log(err);
								});
							else
								this.$router.push({ 
									name: 'login', 
									params: {navError: 'Could not get user state from database, please create an account or contact admin.'} 
								}).catch((err) => {
									console.log(err);
								});
						});
						battleStatusCheck.then(function(result) {
						
							//getting null result
						
							if(typeof(result) === 'object' && result != null) {
								vm.enemyData = result.enemy;
								vm.playerData = result.player;
								vm.engageDistance = result.distance;
								
								vm.drawDistanceGrid(vm);
								vm.generateActiveData('Name', vm.playerData.characterName);
								vm.generateActiveData('Attack', vm.playerData.currentAttack + '/' + vm.playerData.attack);
								vm.generateActiveData('Health', vm.playerData.currentHealth + '/' + vm.playerData.health);
								vm.generateActiveData('Stamina', vm.playerData.currentStamina + '/' + vm.playerData.stamina);
								
								vm.generateActiveDataEnemy(vm.enemyData.name, 'Name');
								vm.generateActiveDataEnemy(vm.enemyData.currentAttack + '/' + vm.enemyData.attack, 'Attack');
								vm.generateActiveDataEnemy(vm.enemyData.currentHealth + '/' + vm.enemyData.health, 'Health');
								vm.generateActiveDataEnemy(vm.enemyData.currentStamina + '/' + vm.enemyData.stamina, 'Stamina');
								
								vm.drawAvatars(vm);
								
								//enable controls
								let all = document.getElementsByTagName("*");
								for (let i = 0, count = all.length; i < count; i++) {
									all[i].style.pointerEvents = 'auto';
								}
							}	
							else {
								//next({name:'rpgGame', params:{message: 'You are not in a battle.'}, replace:true});
								
								vm.$router.push({ 
									name: 'rpgGame', 
									params: {message: 'You are not in a battle.'} 
								}).catch((err) => {
									console.log(err);
								});
							}	
						});
						
					}
					else {
						this.$router.push({ 
							name: 'home', 
							params: {message: 'You need to be logged in to access that resource'} 
						}).catch((err) => {
							console.log(err);
						});
					}
					//enable controls
					let all = document.getElementsByTagName("*");
					for (let i = 0, count = all.length; i < count; i++) {
						all[i].style.pointerEvents = 'auto';
					}
				
				}
				else {
					console.log(this.$route.params);
					
					const vm = this;
					vm.enemyData = vm.$route.params.enemy;
					vm.playerData = vm.$route.params.player;
					vm.engageDistance = vm.$route.params.distance;
					
					vm.drawDistanceGrid(vm);
					vm.generateActiveData('Name', vm.playerData.characterName);
					vm.generateActiveData('Attack', vm.playerData.currentAttack + '/' + vm.playerData.attack);
					vm.generateActiveData('Health', vm.playerData.currentHealth + '/' + vm.playerData.health);
					vm.generateActiveData('Stamina', vm.playerData.currentStamina + '/' + vm.playerData.stamina);
					
					vm.generateActiveDataEnemy(vm.enemyData.name, 'Name');
					vm.generateActiveDataEnemy(vm.enemyData.currentAttack + '/' + vm.enemyData.attack, 'Attack');
					vm.generateActiveDataEnemy(vm.enemyData.currentHealth + '/' + vm.enemyData.health, 'Health');
					vm.generateActiveDataEnemy(vm.enemyData.currentStamina + '/' + vm.enemyData.stamina, 'Stamina');
					
					vm.drawAvatars(vm);
				}
			}
			
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
			meleeEnemy() {
				this.formData = new FormData();
				this.formData.append('_method', 'POST');
	
				const headers = { 
				  'Content-Type': 'multipart/form-data',
				  'enctype' : 'multipart/form-data',
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				}
				
				const meleeEnemy = async function(formData) {
					try {
						let response = await axios({
							method : "POST",
							baseURL: 'http://127.0.0.1:8000/api',
							url    : 'http://127.0.0.1:8000/api/meleeEnemy',
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
				
				meleeEnemy(this.formData)
				.then(response => {
					console.log(response);					
					if(response.data.results.message)
						document.getElementById('messageContainer').textContent = response.data.results.message;
					document.getElementById('playerStamina').textContent = response.data.results.playerNewStamina;
					document.getElementById('enemyHealth').textContent = response.data.results.enemyNewHealth;
					document.getElementById('playerHealth').textContent = response.data.results.playerNewHealth;
					let controls = document.querySelectorAll('.playerControls');
					controls.forEach(function(item) {
						item.classList.add('d-none');	
					});
					let all = document.getElementsByTagName("*");
					for (let i = 0, count = all.length; i < count; i++) {
						all[i].style.pointerEvents = 'none';
					}
					document.getElementById('mapReturn').classList.toggle('d-none');
					document.getElementById('mapReturnButton').style.pointerEvents = 'auto';
				});
			},
			returnToMap() {
				this.$router.push({ 
						name: 'rpgGame'
					}).catch((err) => {
						console.log(err);
					});
			},
			drawAvatars(vm) {
				let playerAvatar = document.createElement('img');
				//playerAvatar.classList.add();
				playerAvatar.setAttribute('src', vm.playerData.avatar);
				document.getElementById('battleScenePlayer').textContent = '';
				document.getElementById('battleScenePlayer').appendChild(playerAvatar);
				
				let enemyAvatar = document.createElement('img');
				//enemyAvatar.classList.add();
				enemyAvatar.setAttribute('src', vm.enemyData.avatar);
				document.getElementById('battleSceneEnemy').textContent = '';
				document.getElementById('battleSceneEnemy').appendChild(enemyAvatar);
			},
			drawDistanceGrid(vm) {
				document.getElementById('distanceGrid').textContent = '';
				let playerItem = document.createElement('p');
				playerItem.classList.add('col-1', 'p-0', 'border', 'border-white');
				playerItem.setAttribute('id', 'square' + 0);
				playerItem.textContent = 'P';
				document.getElementById('distanceGrid').appendChild(playerItem);
				
				for(let i = 1; i <= vm.engageDistance; i++) {
					let gridItem = document.createElement('p');
					gridItem.classList.add('col-1', 'p-0', 'border', 'border-white');
					gridItem.setAttribute('id', 'square' + i);
					gridItem.textContent = '-';
					document.getElementById('distanceGrid').appendChild(gridItem);
				}
				
				let EnemyItem = document.createElement('p');
				EnemyItem.classList.add('col-1', 'p-0', 'border', 'border-white');
				EnemyItem.setAttribute('id', 'square' + 0);
				EnemyItem.textContent = 'E';
				document.getElementById('distanceGrid').appendChild(EnemyItem);
			},
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
				});
				
				this.drawEnemyPositions();
			},
			toggleInventory() {
				document.getElementById('menuDataArea').classList.toggle('d-none');
				
				//closes map controls area
				let controlAreas = document.getElementsByClassName('controlArea');
				for(let i = 0; i < controlAreas.length; i++) {
					controlAreas[i].classList.toggle('d-none');
				}
				
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
					
				let controlAreas = document.getElementsByClassName('controlArea');
				for(let i = 0; i < controlAreas.length; i++) {
					controlAreas[i].classList.toggle('d-none');
				}
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				document.getElementById('closeStatusContainer').classList.toggle('d-none');
				document.getElementById('menuDataArea').classList.toggle('d-none');
			},
			toggleGameMenu() {
				let controlAreas = document.getElementsByClassName('controlArea');
				for(let i = 0; i < controlAreas.length; i++) {
					controlAreas[i].classList.toggle('d-none');
				}
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeGameMenuContainer').classList.toggle('d-none');
			
			},
			toggleInspectMenu() {
				document.getElementById('menuDataArea').classList.toggle('d-none');
				
				//gets status data only when toggle to make status container visible
				if(document.getElementById('closeInspectContainer').classList.contains('d-none'))
					this.populateInspect();
				else
					document.getElementById('menuDataArea').textContent = 'loading data...';
					
				let controlAreas = document.getElementsByClassName('controlArea');
				for (let i = 0; i < controlAreas.length; i++) {
					controlAreas[i].classList.toggle('d-none');
				}
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeInspectContainer').classList.toggle('d-none');
				
			},			
			populateInspect() {
				User.inspectBattleEnemy({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					this.enemyStatusData = response.data.enemy;
					console.log(this.enemyStatusData);
					document.getElementById('menuDataArea').textContent = '';
					
					this.generateDataRow('Name', this.enemyStatusData.name);
					this.generateDataRow('Attack', this.enemyStatusData.currentAttack + '/' + this.enemyStatusData.attack);
					this.generateDataRow('Health', this.enemyStatusData.currentHealth + '/' + this.enemyStatusData.health);
					this.generateDataRow('Stamina', this.enemyStatusData.currentStamina + '/' + this.enemyStatusData.stamina);
					this.generateDataRow('Armour', this.enemyStatusData.armour);
					//this.generateDataRow('Recovery', 'H: ' + this.enemyStatusData.currentHealthRegen + '/' + //this.enemyStatusData.healthRegen
					//	+ ' | ' + 'S: ' + this.enemyStatusData[i].currentstaminaRegen + '/' + this.enemyStatusData.staminaRegen);
					//this.generateDataRow('Agility', this.enemyStatusData.currentAgility + '/' + this.enemyStatusData.agility);
					//this.generateDataRow('Accuracy', this.enemyStatusData.currentAccuracy + '/' + this.enemyStatusData.accuracy);
					//this.generateDataRow('money', this.enemyStatusData.money);
					
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
			generateActiveData(key, data) {
				let dataRowContainer = document.createElement('div');   
				dataRowContainer.classList.add('row');
				
				let dataRowFieldKey = document.createElement('div');   
				dataRowFieldKey.classList.add('col-6', 'text-center');
				dataRowFieldKey.textContent = key;
				dataRowContainer.appendChild(dataRowFieldKey);
				
				let dataRowFieldData = document.createElement('div');
				dataRowFieldData.setAttribute('id', 'player' + key);
				dataRowFieldData.classList.add('col-6', 'text-center');
				dataRowFieldData.textContent = data;
				dataRowContainer.appendChild(dataRowFieldData);
				
				document.getElementById('playerInfo').appendChild(dataRowContainer);
			},
			generateActiveDataEnemy(data, id) {
				let dataRowContainer = document.createElement('div');   
				dataRowContainer.classList.add('row', 'justify-content-center');
				
				let dataRowFieldData = document.createElement('div'); 
				dataRowFieldData.setAttribute('id', 'enemy' + id);
				dataRowFieldData.classList.add('col-6', 'text-center');
				dataRowFieldData.textContent = data;
				dataRowContainer.appendChild(dataRowFieldData);
				
				document.getElementById('enemyInfo').appendChild(dataRowContainer);
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
					this.generateDataRow('Current Avatar', 'Avatar');
					this.generateDataRow('Name', response.data.character.characterName);
					this.generateDataRow('Health', response.data.character.currentHealth + '/' + response.data.character.health);
					this.generateDataRow('Stamina', response.data.character.currentStamina + '/' + response.data.character.stamina);
					this.generateDataRow('Recovery', 'H ' + response.data.character.healthRegen + ' / ' + 'S ' + response.data.character.staminaRegen);
					this.generateDataRow('Agility', response.data.character.agility);
					this.generateDataRow('Accuracy', response.data.character.accuracy);
					this.generateDataRow('Armour', response.data.character.armour);					
					this.generateDataRow('Money', response.data.character.money);					
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