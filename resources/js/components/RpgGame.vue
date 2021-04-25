<template>
    <div class="container-fluid d-flex flex-column text-light">
	
		<header class="row fixed-top">
			<div class="col text-center d-flex">		
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
				</div>	
				<div class="flex-fill w-33 h-75">
					<h3 class="mt-1">Rpg Game</h3>
				</div>	
				<div class="flex-fill w-33">
					<button v-on:click="logout" type="button" class="btn btn-dark flex-fill w-100">Logout</button>
				</div>	
			</div>
		</header>
	
		<div class="row text-center mt-5 mb-2">
			<div id="messageContainer" class="col text-center">
				Messages
			</div>
		</div>
		
		<div class="row mt-5 mb-5" id="gridArea">
			<div class="col">		
				<div>
					<div id="mapGrid" class="col text-center">
						Generating map...
					</div>					
				</div>
			</div>
		</div>
		
		<div class="row mt-5 mb-5" id="controlArea">
			<div class="col-6">		
				<div id="directionGrid" class="text-center">
					<div id="directionplaceholder" class="row mb-4 d-none">
						<div class="col text-center">
							moving...
						</div>
					</div>
					
					<div class="row mb-4 controllerRow">
						<div v-on:click="moveCharacter($event)" id="upLeft" class="col-4"><b-icon icon="arrow-up-left-circle"></b-icon></div>
						<div v-on:click="moveCharacter($event)" id="up" class="col-4"><b-icon icon="arrow-up-circle"></b-icon></div>
						<div v-on:click="moveCharacter($event)" id="upRight" class="col-4"><b-icon icon="arrow-up-right-circle"></b-icon></div>
					</div>
					<div class="row mb-4 controllerRow">
						<div v-on:click="moveCharacter($event)" id="left" class="col-4"><b-icon icon="arrow-left-circle"></b-icon></div>
						<div v-on:click="moveCharacter($event)" id="wait" class="col-4"><b-icon icon="app"></b-icon></div>
						<div v-on:click="moveCharacter($event)" id="right" class="col-4"><b-icon icon="arrow-right-circle"></b-icon></div>
					</div>
					<div class="row mb-4 controllerRow">
						<div v-on:click="moveCharacter($event)" id="downLeft" class="col-4"><b-icon icon="arrow-down-left-circle"></b-icon></div>
						<div v-on:click="moveCharacter($event)" id="down" class="col-4"><b-icon icon="arrow-down-circle"></b-icon></div>
						<div v-on:click="moveCharacter($event)" id="downRight" class="col-4"><b-icon icon="arrow-down-right-circle"></b-icon></div>
					</div>
				</div>				
			</div>
			<div id="actionGrid" class="col-6">
				<div id="inspectDiv" v-on:click="toggleInspectMenu" class="row-9 mb-4 actionRow d-flex justify-content-center">Inspect</div>
				<div id="fightDiv" v-on:click="selectFight" class="row-9 mb-4 actionRow d-flex justify-content-center">Fight</div>
				<div id="skillDiv" class="row-9 mb-4 actionRow d-flex justify-content-center">Skill</div>
				<div id="lootDiv" class="row-9 mb-4 actionRow d-flex justify-content-center">Loot</div>
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
				<div id="closeSelectFightContainer" class="flex-fill w-100 d-none">
					<button id="closeSelectFightButton" v-on:click="toggleSelectFightMenu" type="button" class="btn btn-dark flex-fill w-100">Cancel Fight Select</button>
				</div>
				<div id="closeEnemyTurnContainer" class="flex-fill w-100 d-none">
					<button id="closeEnemyTurnButton" v-on:click="toggleEnemyTurnResult" type="button" class="btn btn-dark flex-fill w-100">Continue</button>
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
				playerAvatar: '',
				playerPosition: '',
				lastPlayerPosition: '',
				lastEnemyPosition: '',
				terrainLayerData: '',
				enemyTerrainLayerData: '',
				playerStatus: '',
				enemyData: '',
				enemyStatusData: '',
				enemyMapPositions: [],
				tempEnemyLastSquareMarker: '',
				previousMessage: ''
			}
		},
		beforeMount() {
			let params = this.$route.params;
			if(!params.playerBattleState) {
				const headers = {
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				}
				
				const getMap = async function() {					
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/getMap',
						params : '',
						data   : '',
						headers: headers,
					})
					return response;
				};
				
				getMap()
				.then(response => {
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
				})
				.catch(error => {
					document.getElementById('messageContainer').textContent = error.response.data;
					return;
				});
			}	
		},
		mounted() {
			let all = document.getElementsByTagName("*");
			for (let i = 0, count = all.length; i < count; i++) {
				all[i].style.pointerEvents = 'auto';
			}
			if(this.$route.params.message) {
				document.getElementById('messageContainer').textContent = this.$route.params.message;
			}
			let currentTurn = this.$route.params.currentTurn;
			this.playerAvatar = this.$route.params.playerAvatar;
			let playerTurnPosition = this.$route.params.playerTurnPosition;
			let playerGameTurns = this.$route.params.playerGameTurns;
			let playerBattleState = this.$route.params.playerBattleState;
			let playerBattleTarget = this.$route.params.playerBattleTarget;
			let enemyTurnPositions = this.$route.params.enemyTurnPositions;
			let currentEnemyMapCoord = this.$route.params.currentEnemyMapCoord;
			
			let currentEnemyActing = null;
			let enemyAction = null;
			
			//if player was in fight from params
			let gameGridSquares = document.getElementsByClassName('gameGridSquare');

			if(currentTurn == playerTurnPosition && playerBattleState == true) {
				let mapCoord = currentEnemyMapCoord;
				
				this.formData = new FormData();
				this.formData.append('mapPosition', mapCoord);
				this.formData.append('_method', 'POST');

				const headers = { 
				  'Content-Type': 'multipart/form-data',
				  'enctype' : 'multipart/form-data',
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				}
				
				const startFight = async function(formData) {					
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/switchFight',
						params : '',
						data   : formData,
						headers: headers,
					})
					return response;
				};
				
				startFight(this.formData)
				.then(response => {
					if(response.data.error != null) {
						for (let i = 0, count = all.length; i < count; i++) {
							all[i].style.pointerEvents = 'auto';
						}
						document.getElementById('messageContainer').textContent = response.data.error;
						return;
					}	
					else {			
						this.$router.push({ 
							name: 'rpgGameBattle', 
							params: {distance: response.data.distance, enemy: response.data.enemy} 
						}).catch((err) => {
							for (let i = 0, count = all.length; i < count; i++) {
								all[i].style.pointerEvents = 'auto';
							}
							
							document.getElementById('messageContainer').textContent = 'There was an error starting a battle.';
							console.log(err);
						});
					}
				})
				.catch(error => {
					document.getElementById('messageContainer').textContent = error.response.data;
					return;
				});
			}
			
			if(currentTurn != playerTurnPosition) {
				this.enemyTurn();
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
				playerIcon.setAttribute('src', this.playerAvatar);   
				playerIcon.classList.toggle('img-fluid');   
				playerSquare.appendChild(playerIcon);
			},
			drawEnemyPositions() {
				const headers = { 
					'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				};

				const getEnemies = async function() {
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/getEnemies',
						params : '',
						data   : '',
						headers: headers,
					});
					return response;
				};
				
				getEnemies()
				.then(response => {
					this.enemyMapPositions = [];
					this.enemyData = response.data.enemies;
					for(let i = 0; i < this.enemyData.length; i++) {			
						//get current coords
						let row = this.enemyData[i].mapPosition[0];
						let column = this.enemyData[i].mapPosition[1];
						let enemySquare = document.getElementById(row + '-' + column);
						
						this.enemyMapPositions.push([row, column]);
						
						//outlines enemy square 
						if(!enemySquare.classList.contains('border-danger')) {
							enemySquare.classList.toggle('border-dark');
							enemySquare.classList.toggle('border-danger');
						}
						
						//draws enemy onto square
						enemySquare.innerHTML = '';
						let enemyIcon = document.createElement('img');   
						enemyIcon.setAttribute('src', this.enemyData[i].avatar);   
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
				/*
				User.getEnemies({
					_method: 'POST', token: sessionStorage.getItem('token')
					}, 
						sessionStorage.getItem('token')
					)
					.then((response) => {
						this.enemyMapPositions = [];
						this.enemyData = response.data.enemies;
						for(let i = 0; i < this.enemyData.length; i++) {			
							//get current coords
							let row = this.enemyData[i].mapPosition[0];
							let column = this.enemyData[i].mapPosition[1];
							let enemySquare = document.getElementById(row + '-' + column);
							
							this.enemyMapPositions.push([row, column]);
							
							//outlines enemy square 
							if(!enemySquare.classList.contains('border-danger')) {
								enemySquare.classList.toggle('border-dark');
								enemySquare.classList.toggle('border-danger');
							}
							
							//draws enemy onto square
							enemySquare.innerHTML = '';
							let enemyIcon = document.createElement('img');   
							enemyIcon.setAttribute('src', this.enemyData[i].avatar);   
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
				*/
			},
			updateEnemyPosition(enemyLastTerrainTreeCover, enemyOldPosition, enemyNewPosition, avatar) {
				let enemyOldSquare = document.getElementById(enemyOldPosition[0] + '-' + enemyOldPosition[1]);							
				let enemyNewSquare = document.getElementById(enemyNewPosition[0] + '-' + enemyNewPosition[1]);							
				
				this.enemyMapPositions = this.enemyMapPositions.filter(item => item === [enemyOldPosition[0],enemyOldPosition[1]]); 
				this.enemyMapPositions.push([enemyNewPosition[0], enemyNewPosition[1]]);
				
				enemyOldSquare.classList.add('border-dark');
				enemyOldSquare.classList.remove('border-danger');
				
				enemyNewSquare.classList.toggle('border-dark');
				enemyNewSquare.classList.toggle('border-danger');
				
				enemyOldSquare.innerHTML = '';
				if(enemyLastTerrainTreeCover == true)
					enemyOldSquare.innerHTML = 'T';
				else
					enemyOldSquare.innerHTML = '-';
				
				//draws enemy onto new square
				enemyNewSquare.innerHTML = '';
				let enemyIcon = document.createElement('img');   
				enemyIcon.setAttribute('src', avatar);   
				enemyIcon.classList.toggle('img-fluid');   
				enemyNewSquare.appendChild(enemyIcon);		
				
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
			clearEnemyPosition(row, column, terrain) {
				let enemySquare = document.getElementById(row + '-' + column);
				
				//reverses outline of enemy square
				enemySquare.classList.add('border-dark');
				enemySquare.classList.remove('border-warning');
				
				//draws terrain data back onto square
				enemySquare.innerHTML = '';
				enemySquare.textContent = terrain;
			},
			enemyTurn() {
				//hides and disables controls
				let all = document.getElementsByTagName("*");
				for (let i = 0, count = all.length; i < count; i++) {
					all[i].style.pointerEvents = 'none';
				}
				
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
		
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeEnemyTurnContainer').classList.toggle('d-none');
				document.getElementById('closeEnemyTurnContainer').style.pointerEvents = 'auto';
				
				document.getElementById('closeEnemyTurnButton').style.color = 'black';
				
				document.getElementById('menuDataArea').textContent = document.getElementById('menuDataArea').textContent + ', enemy deciding...';
				
				document.getElementById('messageContainer').textContent = 'Enemy Turn.';
				
				const headers = { 
					'Content-Type': 'multipart/form-data',
					'enctype' : 'multipart/form-data',
					'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				};
			
				const getGameState = async function(formData) {
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/getGameState',
						params : '',
						data   : formData,
						headers: headers,
					});
					return response;
				};
				
				getGameState(this.formData)
				.then(response => {
					let currentTurn = response.data.currentTurn;
					
					let enemyTurnPositions = response.data.enemyTurnPositions;
					let playerBattleState = response.data.playerBattleState;
					let playerBattleTarget = response.data.playerBattleTarget;
					let playerGameTurns = response.data.playerGameTurns;
					let playerTurnPosition = response.data.playerTurnPosition;
					let currentEnemyActing = null;
					let enemyAction = null;
					
					//calls controller function to process enemy turn
					for(let i = 0; i < Object.keys(enemyTurnPositions).length; i++) {
						if(Object.keys(enemyTurnPositions)[i] == currentTurn) {
							currentEnemyActing = enemyTurnPositions[parseInt(Object.keys(enemyTurnPositions)[0])];
							break;
						}	
					}
					
					this.formData = new FormData();
					this.formData.append('currentEnemyActing', currentEnemyActing);
					this.formData.append('_method', 'POST');
		
					const headers = { 
					  'Content-Type': 'multipart/form-data',
					  'enctype' : 'multipart/form-data',
					  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
					}

					const enemyReturnDecision = async function(formData) {
					
						let response = await axios({
							method : "POST",
							baseURL: 'http://127.0.0.1:8000/api',
							url    : 'http://127.0.0.1:8000/api/gameEnemyTurnDecision',
							params : '',
							data   : formData,
							headers: headers,
						})
						return response;
					};
					
					enemyReturnDecision(this.formData)
					.then(response => {
						let enemyActionObj = response.data.enemyAction;
						enemyAction = enemyActionObj[Object.keys(enemyActionObj)[0]];
						let enemyLastTerrain = response.data.enemyLastTerrain;
						let enemyLastTerrainTreeCover = response.data.enemyLastTerrainTreeCover;
						let enemyOldPosition = response.data.enemyOldPosition;
						this.tempEnemyLastSquareMarker = response.data.enemyOldPosition;
						let enemyNewPosition = response.data.enemyNewPosition;
						let enemyAvatar = response.data.enemyAvatar;
						
						//if move
						if(enemyAction == 'move') {
							this.updateEnemyPosition(enemyLastTerrainTreeCover, enemyOldPosition, enemyNewPosition, enemyAvatar);
							let enemyOldSquare = document.getElementById(this.tempEnemyLastSquareMarker[0] + '-' + this.tempEnemyLastSquareMarker[1]);							
							enemyOldSquare.classList.remove('border-dark');
							enemyOldSquare.classList.add('border-danger');
							
							let all = document.getElementsByTagName("*");
							for (let i = 0, count = all.length; i < count; i++) {
								all[i].style.pointerEvents = 'auto';
							}
							document.getElementById('menuDataArea').textContent = 'Enemy decision: ' + enemyAction + ' from ' + enemyOldPosition + ' to ' + enemyNewPosition;
							document.getElementById('closeEnemyTurnButton').style.color = 'white';
							document.getElementById('closeEnemyTurnButton').style.pointerEvents = 'auto';
						}
						
						//if attack
						if(enemyAction == 'attack') {
							let mapCoord = enemyNewPosition;
							
							const sleep = function sleep(ms) {
								return new Promise(resolve => setTimeout(resolve, ms));
							}
							
							document.getElementById('menuDataArea').textContent = 'Enemy at ' + mapCoord + ' attacks player!';
							
							sleep(1500).then(() => {
								this.formData = new FormData();
								this.formData.append('mapPosition', mapCoord);
								this.formData.append('_method', 'POST');
					
								const headers = { 
								  'Content-Type': 'multipart/form-data',
								  'enctype' : 'multipart/form-data',
								  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
								}
								
								const startFight = function(formData) {
									return axios({
										method : "POST",
										baseURL: 'http://127.0.0.1:8000/api',
										url    : 'http://127.0.0.1:8000/api/switchFight',
										params : '',
										data   : formData,
										headers: headers,
									})
									.then(response => response.data)
									.catch(err => console.error(err))
								};
						
								startFight(this.formData)
								.then(response => {
									if(response.error != null) {
										for (let i = 0, count = all.length; i < count; i++) {
											all[i].style.pointerEvents = 'auto';
										}
										document.getElementById('messageContainer').textContent = response.error;
										return;
									}	
									else {
										this.$router.push({ 
											name: 'rpgGameBattle', 
											params: {distance: response.distance, enemy: response.enemy} 
										}).catch((err) => {
											for (let i = 0, count = all.length; i < count; i++) {
												all[i].style.pointerEvents = 'auto';
											}
											
											document.getElementById('messageContainer').textContent = 'There was an error starting a battle.';
											console.log(err);
										});
									}
								});							
							});
						}
						//if item
						
						//if skill
						
					});				
				});	
			},
			moveCharacter(event) {
				let all = document.getElementsByTagName("*");
				for (let i = 0, count = all.length; i < count; i++) {
					all[i].style.pointerEvents = 'none';
				}
				
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
				
				const moveCharacter = async function(formData) {
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/moveCharacter',
						params : '',
						data   : formData,
						headers: headers,
					});
					return response;
				};
				
				moveCharacter(this.formData)
				.then(response => {
					
					if(response.data.message)
						document.getElementById('menuDataArea').textContent = response.data.message;
					
					this.playerPosition = response.data.playerPosition;
					
					this.clearPlayerPosition();
					this.drawPlayerPosition();
					this.drawEnemyPositions();
					
					//document.getElementById('directionGrid').classList.toggle('d-none');
					let controllerArray = document.getElementsByClassName('controllerRow');
					for(let i = 0; i < controllerArray.length; i++) {
						controllerArray[i].classList.toggle('d-none');
					}
					document.getElementById('directionplaceholder').classList.toggle('d-none');
					this.enemyTurn();
				});
			},
			toggleInventory() {
				let messageBox = document.getElementById('messageContainer');
				if(messageBox.textContent != 'Player Inventory') {
					this.previousMessage = messageBox.textContent;
					messageBox.textContent = 'Player Inventory';
				}
				else {
					messageBox.textContent = this.previousMessage;
				}
				//gets status data only when toggle to make status container visible
				if(document.getElementById('closeStatusContainer').classList.contains('d-none'))
					this.populateInventory();
				else
					document.getElementById('menuDataArea').textContent = 'loading data...';
				
				document.getElementById('gridArea').classList.toggle('d-none');
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
				let messageBox = document.getElementById('messageContainer');
				if(messageBox.textContent != 'Player Status') {
					this.previousMessage = messageBox.textContent;
					messageBox.textContent = 'Player Status';
				}
				else {
					messageBox.textContent = this.previousMessage;
				}
				document.getElementById('gridArea').classList.toggle('d-none');
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
			toggleSelectFightMenu() {
				//enable controls
				let all = document.getElementsByTagName("*");
				for (let i = 0, count = all.length; i < count; i++) {
					all[i].style.pointerEvents = 'auto';
				}
				
				//removes map onclick
				let gameGridSquares = document.getElementsByClassName('gameGridSquare');
				for(let i = 0; i < gameGridSquares.length; i++) {
					gameGridSquares[i].style.pointerEvents = 'none';
					gameGridSquares[i].onclick = null;
				}
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
		
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeSelectFightContainer').classList.toggle('d-none');
				document.getElementById('closeSelectFightContainer').style.pointerEvents = 'auto';
				document.getElementById('closeSelectFightButton').style.pointerEvents = 'auto';
				document.getElementById('menuDataArea').textContent = 'loading data...';
			},
			toggleEnemyTurnResult() {
				//reset marked square
				let enemyOldSquare = document.getElementById(this.tempEnemyLastSquareMarker[0] + '-' + this.tempEnemyLastSquareMarker[1]);							
				enemyOldSquare.classList.add('border-dark');
				enemyOldSquare.classList.remove('border-danger');
				
				//enable controls
				let all = document.getElementsByTagName("*");
				for (let i = 0, count = all.length; i < count; i++) {
					all[i].style.pointerEvents = 'auto';
				}
				
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
		
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeEnemyTurnContainer').classList.toggle('d-none');
				document.getElementById('closeEnemyTurnContainer').style.pointerEvents = 'auto';
				document.getElementById('closeEnemyTurnButton').style.pointerEvents = 'auto';
				document.getElementById('menuDataArea').textContent = 'loading data...';
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
			selectFight() {
				document.getElementById('menuDataArea').textContent = 'Select an enemy to fight.';

				//disable controls
				let all = document.getElementsByTagName("*");
				for (let i = 0, count = all.length; i < count; i++) {
					all[i].style.pointerEvents = 'none';
				}
				
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
		
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeSelectFightContainer').classList.toggle('d-none');
				document.getElementById('closeSelectFightContainer').style.pointerEvents = 'auto';
				document.getElementById('closeSelectFightButton').style.pointerEvents = 'auto';
				document.getElementById('closeSelectFightContainer').style.pointerEvents = 'auto';
				document.getElementById('closeSelectFightButton').style.pointerEvents = 'auto';
				
				document.getElementById('messageContainer').textContent = 'Select enemy.';
				
				let gameGridSquares = document.getElementsByClassName('gameGridSquare');
				
				const vm = this;
				
				for(let i = 0; i < gameGridSquares.length; i++) {
					gameGridSquares[i].style.pointerEvents = 'auto';
					gameGridSquares[i].onclick = function() {
						let all = document.getElementsByTagName("*");

						for (let i = 0, count = all.length; i < count; i++) {
							//all[i].classList.add('d-none');
							all[i].style.pointerEvents = 'none';
						}
							
						let mapCoord = gameGridSquares[i].id.split('-').map(Number);
						
						this.formData = new FormData();
						this.formData.append('mapPosition', mapCoord);
						this.formData.append('_method', 'POST');
			
						const headers = { 
						  'Content-Type': 'multipart/form-data',
						  'enctype' : 'multipart/form-data',
						  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
						}
						
						const startFight = async function(formData, vm) {
							let response = await axios({
								method : "POST",
								baseURL: 'http://127.0.0.1:8000/api',
								url    : 'http://127.0.0.1:8000/api/switchFight',
								params : '',
								data   : formData,
								headers: headers,
							});
							return response;
						};
						
						startFight(this.formData)
						.then(response => {
							if(response.data.message != null) {
								document.getElementById('messageContainer').textContent = response.data.message;
								vm.toggleSelectFightMenu();
							}	
							else {
								vm.$router.push({ 
									name: 'rpgGameBattle', 
									params: {distance: response.data.distance, enemy: response.data.enemy} 
								}).catch((err) => {
									for (let i = 0, count = all.length; i < count; i++) {
										all[i].style.pointerEvents = 'auto';
									}
									
									document.getElementById('messageContainer').textContent = 'There was an error starting a battle.';
									console.log(err);
								});
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
					}	
				};
			},
			populateInventory() {
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
					url    : 'http://127.0.0.1:8000/api/getCharacterInventory',
					params : '',
					data   : this.formData,
					headers: headers,
				}).then(response => {
					document.getElementById('menuDataArea').textContent = '';
					let characterInventory = response.data.characterInventory;
					for(let i = 0; i < characterInventory.length; i++) {
						this.generateClickableInventoryRow(characterInventory[i].name, characterInventory[i].quantity);
					}
				}).catch(error => {
					console.log(error)
				});
			},
			populateInspect() {
				const headers = { 
					'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				};
			
				const inspectEnemies = async function() {
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/inspectEnemies',
						params : '',
						data   : '',
						headers: headers,
					});
					return response;
				};
				
				inspectEnemies()
				.then(response => {
					console.log(response.data.enemies);
					this.enemyStatusData = response.data.enemies;
					document.getElementById('menuDataArea').textContent = '';
					document.getElementById('menuDataArea').textContent = response.data.message;
					
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
			generateClickableInventoryRow(name, quantity) {				
				var vm = this;
				let inventoryRowContainer = document.createElement('div');   
				inventoryRowContainer.classList.add('row');
				let itemName = document.createElement('div');   
				itemName.classList.add('col-6', 'mb-2', 'text-center');
				itemName.setAttribute('id', name);
				itemName.textContent = name;
				itemName.addEventListener('click', function(event) {
					vm.useItem(event.target.id);
				});
				
				inventoryRowContainer.appendChild(itemName);

				let itemQuantity = document.createElement('div'); 
				itemQuantity.classList.add('col-6', 'text-center');
				itemQuantity.textContent = quantity;
				
				inventoryRowContainer.appendChild(itemQuantity);
				
				document.getElementById('menuDataArea').appendChild(inventoryRowContainer);
			
			},
			useItem(name) {
				console.log(name);
				const headers = { 
					'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				};
				this.formData = new FormData();
				this.formData.append('token', sessionStorage.getItem('token'));
				this.formData.append('_method', 'POST');
				this.formData.append('itemName', name);
				
				
				const useItem = async function(formData) {
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/useItem',
						params : '',
						data   : formData,
						headers: headers,
					});
					return response;
				};
			
				useItem(this.formData)
				.then(response => {
					console.log(response);
				})
				.catch(error => {
					console.log(error);
					document.getElementById('messageContainer').textContent = error.response.message;
				});
			},
			generateDataRow(key, data = null, type = 'text') {
				if(type == 'avatar') {
					const headers = { 
						'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
					};
			
					const getAvatar = async function() {
						let response = await axios({
							method : "POST",
							baseURL: 'http://127.0.0.1:8000/api',
							url    : 'http://127.0.0.1:8000/api/getAvatar',
							params : '',
							data   : '',
							headers: headers,
						});
						return response;
					};
				
					getAvatar()
					.then(response => {
						console.log(response);
						let dataRowContainer = document.createElement('div');   
						dataRowContainer.classList.add('row', 'bg-secondary', 'mb-1');
						
						let dataRowAvatar = document.createElement('img');   
						dataRowAvatar.classList.add('col', 'img-fluid');
						dataRowAvatar.src = response.data.playerAvatar; 
						dataRowContainer.append(dataRowAvatar);
						
						document.getElementById('menuDataArea').prepend(dataRowContainer);
					})
					.catch(error => {
						console.log(error);
						document.getElementById('messageContainer').textContent = error.response.message;
					});
				}
				else if(type == 'scoreHeader') {
					let dataRowContainer = document.createElement('div');   
					dataRowContainer.classList.add('row');
					
					let headerTextContainer = document.createElement('div'); 
					headerTextContainer.classList.add('col-12', 'text-center');
					headerTextContainer.textContent = key;
					dataRowContainer.appendChild(headerTextContainer);
					
					document.getElementById('menuDataArea').appendChild(dataRowContainer);
				}
				else {
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
				}
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
					this.generateDataRow('Current Avatar', null, 'avatar');
					this.generateDataRow('Name', response.data.character.characterName);
					this.generateDataRow('Health', response.data.character.currentHealth + '/' + response.data.character.health);
					this.generateDataRow('Stamina', response.data.character.currentStamina + '/' + response.data.character.stamina);
					this.generateDataRow('Recovery', 'H ' + response.data.character.healthRegen + ' / ' + 'S ' + response.data.character.staminaRegen);
					this.generateDataRow('Agility', response.data.character.agility);
					this.generateDataRow('Accuracy', response.data.character.accuracy);
					this.generateDataRow('Armour', response.data.character.armour);					
					this.generateDataRow('Money', response.data.character.money);
					this.generateDataRow('Score', null, 'scoreHeader');
					this.generateDataRow('Damage Dealt', response.data.character.damageDealt);
					this.generateDataRow('Damage Received', response.data.character.damageReceived);
					this.generateDataRow('Kill Count', response.data.character.enemiesKilled);
					this.generateDataRow('Item Usage Count', response.data.character.itemsUsed);
					this.generateDataRow('All Time Earnings', response.data.character.totalEarnings);
					this.generateDataRow('Squares Travelled', response.data.character.squaresMoved);
					this.generateDataRow('Total Score', response.data.character.score);
				}).catch(error => {
					console.log(error)
				});
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