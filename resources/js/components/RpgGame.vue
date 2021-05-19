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
	
		<div class="row mt-5 mb-2">
			<div id="messageContainer" class="col overflow-auto" style="white-space:pre;height:40px">
			</div>
		</div>
		
		<div class="row mt-1 mb-5" id="gridArea">
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
				<div id="lootDiv" v-on:click="toggleLootMenu" class="row-9 mb-4 actionRow d-flex justify-content-center">Loot</div>
			</div>
		</div>
		
		<!--div class="row mt-5 mb-5"-->
			<div class="overflow-auto text-center d-none" id="menuDataArea">loading data...</div>
		<!--/div-->
		
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
				<div id="closeLootContainer" class="flex-fill w-100 d-none">
					<button v-on:click="toggleLootMenu" type="button" class="btn btn-dark flex-fill w-100">Close Loot</button>
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
					if(!localStorage.hasOwnProperty('gameLog'))
						localStorage.setItem('gameLog', error.response.data + '\r\n');
					else
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.data + '\r\n');
					document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
					document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
					return;
				});
			}	
		},
		mounted() {
			if(!localStorage.hasOwnProperty('gameLog'))
				localStorage.setItem('gameLog', 'Welcome to rpgGame.');
			else
				localStorage.setItem('gameLog', localStorage.getItem('gameLog'));
			document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
			
			setTimeout(function(){
				document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
			}, 500); 
			
			let all = document.getElementsByTagName("*");
			for (let i = 0, count = all.length; i < count; i++) {
				all[i].style.pointerEvents = 'auto';
			}
			if(this.$route.params.message) {
				if(!localStorage.hasOwnProperty('gameLog'))
					localStorage.setItem('gameLog', this.$route.params.message + '\r\n');
				else
					localStorage.setItem('gameLog', localStorage.getItem('gameLog') + this.$route.params.message + '\r\n');
				document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
				document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
						if(!localStorage.hasOwnProperty('gameLog'))
							localStorage.setItem('gameLog', response.data.error + '\r\n');
						else
							localStorage.setItem('gameLog', localStorage.getItem('gameLog') + response.data.error + '\r\n');
						document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
						document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
							if(!localStorage.hasOwnProperty('gameLog'))
								localStorage.setItem('gameLog', 'There was an error starting a battle.\r\n');
							else
								localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'There was an error starting a battle.\r\n');
							document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
							document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
							console.log(err);
						});
					}
				})
				.catch(error => {
					if(!localStorage.hasOwnProperty('gameLog'))
						localStorage.setItem('gameLog', error.response.data + '\r\n');
					else
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.data + '\r\n');
					document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
					document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
						
						//prevent drawing over player
						if(enemySquare.classList.contains('border-warning') == true || enemySquare.firstElementChild != null) {
							continue;
						}
						
						this.enemyMapPositions.push([row, column]);
						
						//outlines enemy square
						if(this.enemyData[i].currentHealth > 0) {
							if(!enemySquare.classList.contains('border-danger')) {
								enemySquare.classList.toggle('border-dark');
								enemySquare.classList.toggle('border-danger');
							}
						}
						
						//draws enemy onto square
						enemySquare.innerHTML = '';
						let enemyIcon = document.createElement('img');
						if(this.enemyData[i].currentHealth > 0)
							enemyIcon.setAttribute('src', this.enemyData[i].avatar);   
						else
							enemyIcon.setAttribute('src', '/img/rpgGame/gameCharacterGraphics/gravestone.png');
						enemyIcon.classList.toggle('img-fluid');
						enemySquare.appendChild(enemyIcon);
						
					}
				})
				.catch(error => {
					//server response errors
					if (error.response) {
						console.log(error.response.data.message);
						if(!localStorage.hasOwnProperty('gameLog'))
							localStorage.setItem('gameLog', error.response.data.message + '\r\n');
						else
							localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.data.message + '\r\n');
						document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
						document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
				
				if(document.getElementById('menuDataArea').classList.contains('d-none'))
					document.getElementById('menuDataArea').classList.toggle('d-none');
				document.getElementById('menuDataArea').textContent = document.getElementById('menuDataArea').textContent + 'enemy deciding...';
				
				if(!localStorage.hasOwnProperty('gameLog'))
					localStorage.setItem('gameLog', '\r\nEnemy turn\r\n');
				else
					localStorage.setItem('gameLog', localStorage.getItem('gameLog') + '\r\nEnemy turn\r\n');
				document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
				document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
			
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
					
					let msg;
					enemyReturnDecision(this.formData)
					.then(response => {
						console.log(response);
						msg = response.data.results.message;
						let enemyActionObj = response.data.enemyAction;
						enemyAction = enemyActionObj[Object.keys(enemyActionObj)[0]];
						let enemyLastTerrain = response.data.enemyLastTerrain;
						let enemyLastTerrainTreeCover = response.data.enemyLastTerrainTreeCover;
						let enemyOldPosition = response.data.enemyOldPosition;
						this.tempEnemyLastSquareMarker = response.data.enemyOldPosition;
						let enemyNewPosition = response.data.enemyNewPosition;
						let enemyAvatar = response.data.enemyAvatar;
						
						//if dead
						if(enemyActionObj == 'Dead') {
							let enemyName = response.data.enemyName;
							let enemyOldSquare = document.getElementById(enemyOldPosition[0] + '-' + enemyOldPosition[1]);
							let graveStoneImage = '/img/rpgGame/gameCharacterGraphics/gravestone.png';
							if(enemyOldSquare.hasChildNodes() && enemyOldSquare.firstElementChild.src == null)
								enemyOldSquare.firstElementChild.src = graveStoneImage;
							
							if(!localStorage.hasOwnProperty('gameLog'))
								localStorage.setItem('gameLog', 'Turn passed: ' + enemyName + ' at ' + enemyOldPosition + ' is dead.' + '\r\n');
							else
								localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'Turn passed: ' + enemyName + ' at ' + enemyOldPosition + ' is dead.' + '\r\n');
							document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
							document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
							
							let all = document.getElementsByTagName("*");
							for (let i = 0, count = all.length; i < count; i++) {
								all[i].style.pointerEvents = 'auto';
							}
							document.getElementById('menuDataArea').textContent = 'Turn passed: ' + enemyName + ' at ' + enemyOldPosition + ' is dead.';
							document.getElementById('closeEnemyTurnButton').style.color = 'white';
							document.getElementById('closeEnemyTurnButton').style.pointerEvents = 'auto';
							
						}
						
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
							document.getElementById('menuDataArea').textContent = 'Enemy decision: ' + enemyAction + ' from ' + enemyOldPosition + ' to ' + 	enemyNewPosition;
							
							if(!localStorage.hasOwnProperty('gameLog'))
								localStorage.setItem('gameLog', 'Enemy decision: ' + enemyAction + ' from ' + enemyOldPosition + ' to ' + 	enemyNewPosition + '\r\n');
							else
								localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'Enemy decision: ' + enemyAction + ' from ' + enemyOldPosition + ' to ' + enemyNewPosition + '\r\n');
							document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
							document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
							
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
							
							if(!localStorage.hasOwnProperty('gameLog'))
								localStorage.setItem('gameLog', 'Enemy at ' + mapCoord + ' attacks player!' + '\r\n');
							else
								localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'Enemy at ' + mapCoord + ' attacks player!' + '\r\n');
							document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
							document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
							
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
										if(!localStorage.hasOwnProperty('gameLog'))
											localStorage.setItem('gameLog', response.error + '\r\n');
										else
											localStorage.setItem('gameLog', localStorage.getItem('gameLog') + response.error + '\r\n');
										document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
										document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
											if(!localStorage.hasOwnProperty('gameLog'))
												localStorage.setItem('gameLog', 'There was an error starting a battle.\r\n');
											else
												localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'There was an error starting a battle.\r\n');
											document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
											document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
											console.log(err);
										});
									}
								});							
							});
						}
						//if item
						
						//if skill
						
						//enemy end of turn updates
						let msgPeriodIndex = msg.indexOf('.');
						let processedMsg;
						
						if(msgPeriodIndex != -1)
							processedMsg = msg.replace(/\./g, '\r\n').slice(0, -2);
						else
							processedMsg = response.data.message;
						
						if(!localStorage.hasOwnProperty('gameLog'))
							localStorage.setItem('gameLog', processedMsg);
						else
							localStorage.setItem('gameLog', localStorage.getItem('gameLog') + processedMsg);
							
						document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
						document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
					if(response.data.message) {
						let msg = response.data.message;
						let msgPeriodIndex = msg.indexOf('.');
						let processedMsg;
						
						if(msgPeriodIndex != -1)
							processedMsg = msg.replace(/\./g, '\r\n').slice(0, -2);
						else
							processedMsg = response.data.message;
						
						if(!localStorage.hasOwnProperty('gameLog'))
							localStorage.setItem('gameLog', processedMsg);
						else
							localStorage.setItem('gameLog', localStorage.getItem('gameLog') + processedMsg);
						
						document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
						document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
					}	
					
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
				if(!localStorage.hasOwnProperty('gameLog'))
					localStorage.setItem('gameLog', 'Toggled inventory.\r\n');
				else
					localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'Toggled inventory.\r\n');
				document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
				document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
				
				if(!document.getElementById('closeInventoryContainer').classList.contains('d-none'))
					document.getElementById('menuDataArea').textContent = '';
				else {
					document.getElementById('menuDataArea').textContent = 'loading data...';
					this.populateInventory();				
				}
				document.getElementById('messageContainer').classList.toggle('d-none');
				document.getElementById('gridArea').classList.toggle('d-none');
				//closes map controls area
				document.getElementById('controlArea').classList.toggle('d-none');
				document.getElementById('menuDataArea').classList.toggle('d-none');
				
				//closes bottom game menu bar
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				//shows target meny button
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				document.getElementById('closeInventoryContainer').classList.toggle('d-none');
			},
			toggleStatus() {
				if(!localStorage.hasOwnProperty('gameLog'))
					localStorage.setItem('gameLog', 'Toggled status page.\r\n');
				else
					localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'Toggled status page.\r\n');
				document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
				document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
				document.getElementById('gridArea').classList.toggle('d-none');
				if(document.getElementById('closeStatusContainer').classList.contains('d-none'))
					this.populateStatus();
				else
					document.getElementById('menuDataArea').textContent = 'loading data...';
					
				document.getElementById('controlArea').classList.toggle('d-none');
				document.getElementById('menuDataArea').classList.toggle('d-none');
				document.getElementById('menuDataArea').classList.toggle('mb-2');
				document.getElementById('messageContainer').classList.toggle('d-none');
				
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
				
				if(!enemyOldSquare.classList.contains('border-warning')) {
					enemyOldSquare.classList.add('border-dark');
					enemyOldSquare.classList.remove('border-danger');
					//enemyOldSquare.classList.remove('border-warning');
				}
			
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
				document.getElementById('menuDataArea').classList.toggle('d-none');
				
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
					
					if(currentTurn != playerTurnPosition)
						this.enemyTurn();
				});
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
				document.getElementById('menuDataArea').classList.toggle('d-none');
				if(!document.getElementById('closeInspectContainer').classList.contains('d-none'))
					document.getElementById('menuDataArea').textContent = '';
				else
					this.populateInspect();				
				
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeInspectContainer').classList.toggle('d-none');
				
			},
			toggleLootMenu() {
				document.getElementById('menuDataArea').classList.toggle('d-none');
				if(!document.getElementById('closeLootContainer').classList.contains('d-none'))
					document.getElementById('menuDataArea').textContent = '';
				else
					this.populateLoot();				
				
				document.getElementById('controlArea').classList.toggle('d-none');
				
				document.getElementById('bottomMenuBar').classList.toggle('d-none');
				document.getElementById('bottomMenuBar').classList.toggle('d-flex');
				
				document.getElementById('currentMenuControl').classList.toggle('d-none');
				document.getElementById('currentMenuControl').classList.toggle('d-flex');
				
				document.getElementById('closeLootContainer').classList.toggle('d-none');
				
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
				
				if(!localStorage.hasOwnProperty('gameLog'))
					localStorage.setItem('gameLog', 'Selecting enemy to fight.\r\n');
				else
					localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'Selecting enemy to fight.\r\n');
				document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
				document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
				
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
								if(!localStorage.hasOwnProperty('gameLog'))
									localStorage.setItem('gameLog', response.data.message + '\r\n');
								else
									localStorage.setItem('gameLog', localStorage.getItem('gameLog') + response.data.message + '\r\n');
								document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
								document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
									if(!localStorage.hasOwnProperty('gameLog'))
										localStorage.setItem('gameLog', 'There was an error starting a battle.\r\n');
									else
										localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'There was an error starting a battle.\r\n');
									document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
									document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
									console.log(err);
								});
							}
						})
						.catch(error => {
							//server response errors
							if (error.response) {
								console.log(error.response.data.message);
								if(!localStorage.hasOwnProperty('gameLog'))
									localStorage.setItem('gameLog', error.response.data.message + '\r\n');
								else
									localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.data.message + '\r\n');
								document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
								document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
					let headSpacer = document.createElement('div'); 
					headSpacer.classList.add('text-center');
					headSpacer.textContent = 'Inventory';
					document.getElementById('menuDataArea').appendChild(headSpacer);
					let characterInventory = response.data.characterInventory;
					if(characterInventory.length == 0)
						document.getElementById('menuDataArea').textContent = 'Empty';
					else	
						for(let i = 0; i < characterInventory.length; i++) {
							this.generateClickableInventoryRow(characterInventory[i]);
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
					//console.log(response.data.enemies);
					this.enemyStatusData = response.data.enemies;
					document.getElementById('menuDataArea').textContent = '';
					document.getElementById('menuDataArea').textContent += response.data.message + '\r\n';
					if(!localStorage.hasOwnProperty('gameLog'))
						localStorage.setItem('gameLog', 'Inspected enemies.\r\n');
					else
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'Inspected enemies.\r\n');
					document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
					document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
					for(let i = 0; i < this.enemyStatusData.length; i++) {
						this.generateDataRow('Name', this.enemyStatusData[i].name);
						this.generateDataRow('Direction', this.enemyStatusData[i].mapOrientation);
						this.generateDataRow('Attack', this.enemyStatusData[i].currentAttack + '/' + this.enemyStatusData[i].attack);
						this.generateDataRow('Health', this.enemyStatusData[i].currentHealth + '/' + this.enemyStatusData[i].health);
						this.generateDataRow('Stamina', this.enemyStatusData[i].currentStamina + '/' + this.enemyStatusData[i].stamina);
					}
				})
				.catch(error => {
					//server response errors
					if (error.response) {
						console.log(error.response.data.message);
						if(!localStorage.hasOwnProperty('gameLog'))
							localStorage.setItem('gameLog', error.response.data.message + '\r\n');
						else
							localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.data.message + '\r\n');
						document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
						document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
			populateLoot() {
				const headers = { 
					'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				};
			
				const lootEnemy = async function() {
					let response = await axios({
						method : "POST",
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/lootEnemy',
						params : '',
						data   : '',
						headers: headers,
					});
					return response;
				};
				
				lootEnemy()
				.then(response => {
					document.getElementById('menuDataArea').textContent = '';
					document.getElementById('menuDataArea').textContent += response.data.results.message + '\r\n';
					if(!localStorage.hasOwnProperty('gameLog')) {
						localStorage.setItem('gameLog', 'Looted area.\r\n');
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + response.data.results.message + '\r\n');
					}
					else {
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + 'Looted area.\r\n');
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + response.data.results.message + '\r\n');
					}	
					document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
					document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
				})
				.catch(error => {
					//server response errors
					if (error.response) {
						console.log(error.response.data.message);
						if(!localStorage.hasOwnProperty('gameLog'))
							localStorage.setItem('gameLog', error.response.data.message + '\r\n');
						else
							localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.data.message + '\r\n');
						document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
						document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
			generateClickableInventoryRow(item) {				
				var vm = this;
				let inventoryRowContainer = document.createElement('div');   
				inventoryRowContainer.classList.add('row');
				
				let itemName = document.createElement('div');   
				itemName.classList.add('col-6', 'mb-2', 'text-center');
				itemName.textContent = item.name;
				itemName.setAttribute('id', (item.name).replace(/\s+/g,'') + 'Row');
				itemName.addEventListener('click', function(event) {
					vm.expandItem(event.target.id);
				});
				
				inventoryRowContainer.appendChild(itemName);

				let itemQuantity = document.createElement('div'); 
				itemQuantity.classList.add('col-6', 'text-center');
				itemQuantity.textContent = item.quantity;
				
				inventoryRowContainer.appendChild(itemQuantity);
				
				let itemDetail = document.createElement('div'); 
				itemDetail.classList.add('row', 'd-none');
				itemDetail.setAttribute('id', (item.name).replace(/\s+/g,'') + 'Detail');
				inventoryRowContainer.appendChild(itemDetail);
				
				//to item details
				//description
				let itemDescription = document.createElement('div'); 
				itemDescription.classList.add('col-12', 'text-center');
				itemDescription.textContent = item.description;
				itemDetail.appendChild(itemDescription);
				
				//effect granted
				let itemEffect = document.createElement('div'); 
				itemEffect.classList.add('col-12', 'text-center');
				itemEffect.textContent = 'Effect: ' + item.effect;
				itemDetail.appendChild(itemEffect);
				
				//duration
				let itemDuration = document.createElement('div'); 
				itemDuration.classList.add('col-12', 'text-center');
				itemDuration.textContent = 'Duration: ' + item.effectDuration;
				itemDetail.appendChild(itemDuration);
				
				//percentage effect
				let itemPercentage = document.createElement('div'); 
				itemPercentage.classList.add('col-12', 'text-center');
				itemPercentage.textContent = 'Effect Percentage: ' + item.effectPercent;
				itemDetail.appendChild(itemPercentage);
				
				//effect stack amount
				let itemStackQuantity = document.createElement('div'); 
				itemStackQuantity.classList.add('col-12', 'text-center');
				itemStackQuantity.textContent = 'Stacks: ' + item.effectStackAmount;
				itemDetail.appendChild(itemStackQuantity);
				
				//effect stack max
				let itemStackMax = document.createElement('div'); 
				itemStackMax.classList.add('col-12', 'text-center');
				itemStackMax.textContent = 'Stacks Max: ' + item.effectStackLimit;
				itemDetail.appendChild(itemStackMax);
				
				//shop value
				let itemvalue = document.createElement('div'); 
				itemvalue.classList.add('col-12', 'text-center');
				itemvalue.textContent = 'Value: ' + item.shopValue;
				itemDetail.appendChild(itemvalue);
				
				//confirm reminder
				let reminder = document.createElement('div'); 
				reminder.classList.add('col-12', 'text-center', 'bg-success');
				reminder.textContent = 'Click again to activate.';
				reminder.setAttribute('id', item.name);
				reminder.addEventListener('click', function(event) {
					vm.useItem(event.target.id);
				});
				itemDetail.appendChild(reminder);
				
				document.getElementById('menuDataArea').appendChild(inventoryRowContainer);
			
			},
			expandItem(target) {
				let targetDiv = target.substring(0, target.length - 3) + 'Detail';
				document.getElementById(target).classList.toggle('bg-secondary');
				document.getElementById(targetDiv).classList.toggle('bg-secondary');
				document.getElementById(targetDiv).classList.toggle('d-none');
			},
			useItem(name) {
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
					console.log(response.data.results);
					this.toggleInventory();
					let itemResults = response.data.results;
					let newLinePosition = itemResults.indexOf('.') + 1;
					let processedItemResults = itemResults.slice(0,newLinePosition) + '\r\n' + itemResults.slice(newLinePosition);
					
					if(!localStorage.hasOwnProperty('gameLog'))
						localStorage.setItem('gameLog', processedItemResults + '\r\n');
					else
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + processedItemResults + '\r\n');
					document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
					document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
				})
				.catch(error => {
					console.log(error);
					if(!localStorage.hasOwnProperty('gameLog'))
						localStorage.setItem('gameLog', error.response.message + '\r\n');
					else
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.message + '\r\n');
					document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
					document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
					
				});
				
				const sleep = function sleep(ms) {
					return new Promise(resolve => setTimeout(resolve, ms));
				}
				
				sleep(1500).then(() => {
					this.enemyTurn();
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
						let dataRowContainer = document.createElement('div');   
						dataRowContainer.classList.add('row', 'bg-secondary', 'mb-1');
						let dataRowAvatar = document.createElement('img');   
						dataRowAvatar.classList.add('col', 'img-fluid');
						dataRowAvatar.src = response.data.playerAvatar;
						dataRowAvatar.id = 'playerAvatar';
						document.getElementById('menuDataArea').classList.toggle('d-none');
						dataRowContainer.append(dataRowAvatar);
						document.getElementById('menuDataArea').prepend(dataRowContainer);
					})
					.catch(error => {
						console.log(error);
						if(!localStorage.hasOwnProperty('gameLog'))
							localStorage.setItem('gameLog', error.response.message + '\r\n');
						else
							localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.message + '\r\n');
						document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
						document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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
				
				
				const getCharacterStatus = async function(formData) {
					let response = await axios({
						method : 'POST',
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/getCharacterStatus',
						params : '',
						data   : formData,
						headers: headers,
					});
					return response;
				};
			
				getCharacterStatus(this.formData)
				.then(response => {
					this.playerStatus = response.data.character;
					document.getElementById('menuDataArea').textContent = '';
					document.getElementById('menuDataArea').classList.toggle('d-none');
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
				})
				.catch(error => {
					console.log(error);
					if(!localStorage.hasOwnProperty('gameLog'))
						localStorage.setItem('gameLog', error.response.message + '\r\n');
					else
						localStorage.setItem('gameLog', localStorage.getItem('gameLog') + error.response.message + '\r\n');
					document.getElementById('messageContainer').textContent = localStorage.getItem('gameLog');
					document.getElementById('messageContainer').scrollTop = document.getElementById('messageContainer').scrollHeight;
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