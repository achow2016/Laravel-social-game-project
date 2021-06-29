<template>
	

    <div class="container-fluid d-flex flex-column text-light">	
		<header style="opacity:1" class="row fixed-top">
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
		
		<div id="squareDetailsContainer" class="row mt-5 mb-2 position-fixed d-none">
			<div id="squareDetails" class="col overflow-auto">
			
			</div>
		</div>
		
		<div class="row mt-5 mb-2">
			<div id="scoreContainer" class="col text-center">
			
				<div class="row">
					<div class="col-4 text-center mb-2">User</div>
					<div class="col-4 text-center mb-2">Chara</div>
					<div class="col-4 text-center mb-2">Score</div>
				</div>
			
			</div>
		</div>
		
		<div>
			<div class="row">
				<div class="col">
					<div id="scoreDetailContainer" class="col text-center">
					
					</div>
				</div>	
			</div>
			<div class="row">
				<div class="col">
					<div id="mapGrid" class="col text-center mt-1 mb-3">
					
					</div>
				</div>	
			</div>
		</div>
		
		<div style="opacity:.5" id="currentMenuControl" class="col text-center d-none fixed-bottom">
			<div id="closeScoreDetailContainer" class="flex-fill w-100 d-none">
				<button v-on:click="toggleScoreDetail" type="button" class="btn btn-dark flex-fill w-100">Close</button>
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
				let characterScores = response.data.scores;
				if(characterScores.length == 0)
						document.getElementById('scoreContainer').textContent = 'No Records';
				else	
					for(let i = 0; i < characterScores.length; i++) {
						this.generateClickableScoreRow(characterScores[i]);
					}
			});
			
		},
		methods: {
			drawPlayerPosition(playerPosition, playerAvatar) {
				let lastPlayerPosition = JSON.parse(playerPosition);
				let row = lastPlayerPosition[0];
				let column = lastPlayerPosition[1];
				let playerSquare = document.getElementById(row + '-' + column);
				
				//outlines player square
				playerSquare.classList.toggle('border-dark');
				playerSquare.classList.toggle('border-warning');
				
				//draws player onto square
				playerSquare.innerHTML = '';
				let playerIcon = document.createElement('img');   
				playerIcon.setAttribute('src', playerAvatar);   
				playerIcon.classList.toggle('img-fluid');   
				playerSquare.appendChild(playerIcon);
			},
			enemySquareDetail(name) {
				console.log(name);
				let idArray = name.split(' ');
				let enemies = [];
				for(let i = 0; i < idArray.length; i++) {
					if(idArray[i] != 'avatar' && Number.isInteger(parseInt(idArray[i].charAt(0))) != true)
						enemies.push(idArray[i]);
				}
				console.log(enemies);
				document.getElementById('squareDetailsContainer').classList.toggle('d-none');
				let squareDetailsDiv = document.getElementById('squareDetails');
				let dataRowContainer = document.createElement('div');   
				dataRowContainer.classList.add('row', 'bg-secondary', 'mb-1');
				let dataRowText = document.createElement('p');
				dataRowText.classList.add('col', 'text-center');
				dataRowText.style.margin = 0;
				dataRowText.textContent = enemies;
				dataRowContainer.append(dataRowText);
				console.log(dataRowContainer);
				squareDetailsDiv.append(dataRowContainer);
				
				const sleep = function sleep(ms) {
					return new Promise(resolve => setTimeout(resolve, ms));
				}
				
				sleep(1500).then(() => {
					document.getElementById('squareDetailsContainer').classList.toggle('d-none');
					document.getElementById('squareDetails').textContent = '';
				});	
			},
			drawEnemyPositions(enemies, playerPosition) {
				let enemyMapPositions = [];
				let enemyData = JSON.parse(enemies);
				let vm = this;
				
				for(let i = 0; i < enemyData.length; i++) {			
					//get current coords
					let row = enemyData[i].mapPosition[0];//add enemy detail on click
					let column = enemyData[i].mapPosition[1];
					let enemySquare = document.getElementById(row + '-' + column);
					enemySquare.id = enemyData[i].name + ' ' + enemySquare.id;
					
					enemySquare.addEventListener('click', function(event) {
						vm.enemySquareDetail(event.target.id);
					});

					//prevent drawing over player
					enemyMapPositions.push([row, column]);
					
					//outlines enemy square
					if(enemyData[i].currentHealth > 0) {
						if(!enemySquare.classList.contains('border-danger')) {
							enemySquare.classList.toggle('border-dark');
							enemySquare.classList.toggle('border-danger');
						}
					}
					
					//remove drawing this enemy if enemy is dead and is sharing a space with a live one. Searches once only.
					let enemyOverlapFound = false;
					
					let enemyPosition = enemyData[i].mapPosition;
					let playerPosition = playerPosition;
		
					//array equals function
					//https://www.30secondsofcode.org/blog/s/javascript-array-comparison
					
					const equals = (a, b) =>
					a.length === b.length &&
					a.every((v, i) => v === b[i]);
		
					if(enemyData[i].currentHealth <= 0 && enemyOverlapFound == false) {
						if(enemyData.length > 1) {
							for(let j = 0; j < enemyData.length; j++) {
							
								enemyPosition = JSON.parse(JSON.stringify(enemyData[i].mapPosition));
					
								if(equals(enemyPosition, playerPosition) || (enemyPosition == enemyData[j].mapPosition && enemyData[i].id != enemyData[j].id)) {
									enemyOverlapFound = true;
									continue;
								}
							}
						}
					}
					
					if(enemyOverlapFound == false) {
						//draws enemy onto square
						enemySquare.innerHTML = '';
						
						let enemyIcon = document.createElement('img');
						if(enemyData[i].currentHealth > 0)
							enemyIcon.setAttribute('src', enemyData[i].avatar);   
						else
							enemyIcon.setAttribute('src', '/img/rpgGame/gameCharacterGraphics/gravestone.png');
						enemyIcon.classList.toggle('img-fluid');
						enemyIcon.id = enemyData[i].name + ' avatar';
					
						enemySquare.appendChild(enemyIcon);
					}
					
				}
		
			},
			generateCharacterGameMap(mapData) {
				this.mapData = JSON.parse(mapData);
				
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
			},
			populateScoreDetails(id) {
				let targetUser = id;
				
				this.formData = new FormData();
				this.formData.append('token', sessionStorage.getItem('token'));
				this.formData.append('_method', 'POST');
				this.formData.append('ownerUser', targetUser);
				
				const headers = { 
				  'Content-Type': 'application/json',
				  'enctype' : 'application/x-www-form-urlencoded',
				  'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
				}
				
				const getCharacterScoreDetails = async function(formData) {
					let response = await axios({
						method : 'POST',
						baseURL: 'http://127.0.0.1:8000/api',
						url    : 'http://127.0.0.1:8000/api/getCharacterScoreDetails',
						params : '',
						data   : formData,
						headers: headers,
					});
					return response;
				};
			
				getCharacterScoreDetails(this.formData)
				.then(response => {
					console.log(response);
				
					this.playerStatus = response.data.details;
					document.getElementById('scoreDetailContainer').textContent = '';
					this.generateDataRow('Current Avatar', response.data.details.characterName, 'avatar');
					this.generateDataRow('Name', response.data.details.characterName);
					this.generateDataRow('Race', response.data.details.race);
					this.generateDataRow('Class', response.data.details.class);
					this.generateDataRow('Health', response.data.details.health);
					this.generateDataRow('Stamina', response.data.details.stamina);
					this.generateDataRow('Recovery', 'H ' + response.data.details.healthRegen + ' / ' + 'S ' + response.data.details.staminaRegen);
					this.generateDataRow('Agility', response.data.details.agility);
					this.generateDataRow('Accuracy', response.data.details.accuracy);
					this.generateDataRow('Armour', response.data.details.armour);					
					this.generateDataRow('Money', response.data.details.money);
					this.generateDataRow('Score', null, 'scoreHeader');
					this.generateDataRow('Damage Dealt', response.data.details.damageDealt);
					this.generateDataRow('Damage Received', response.data.details.damageReceived);
					this.generateDataRow('Kill Count', response.data.details.enemiesKilled);
					this.generateDataRow('Item Usage Count', response.data.details.itemsUsed);
					this.generateDataRow('All Time Earnings', response.data.details.totalEarnings);
					this.generateDataRow('Squares Travelled', response.data.details.squaresMoved);
					this.generateDataRow('Total Score', response.data.details.score);
					this.generateDataRow('Equipment', null, 'equipmentHeader');
					this.generateDataRow('Weapon', response.data.details.weapon);
					this.generateDataRow('Off-Hand', response.data.details.offhandEquipment);
					this.generateDataRow('Body', response.data.details.bodyEquipment);
					this.generateDataRow('Head', response.data.details.headEquipment);
					this.generateDataRow('Arms', response.data.details.armsEquipment);
					this.generateDataRow('Legs', response.data.details.legsEquipment);
					this.generateCharacterGameMap(response.data.details.mapData);						
					this.drawPlayerPosition(response.data.details.playerMapPosition, response.data.details.avatar);
					this.drawEnemyPositions(response.data.details.enemyMapPositions, response.data.details.playerMapPosition);
				})
				.catch(error => {
					console.log(error);
				});
			
			},
			toggleScoreDetail(event) {
				if(document.getElementById('closeScoreDetailContainer').classList.contains('d-none'))
					this.populateScoreDetails(event);
				else
					document.getElementById('scoreDetailContainer').textContent = '';
					
				var scoreContainer = document.getElementById('scoreContainer');
				scoreContainer.classList.toggle('d-none');
				if(scoreContainer.parentElement.classList.contains('mt-5'))
					scoreContainer.parentElement.classList.remove('mt-5', 'mb-2');
				else
					scoreContainer.parentElement.classList.add('mt-5', 'mb-2');
					
				var messageContainer = document.getElementById('messageContainer');
				messageContainer.classList.toggle('d-none');
				if(messageContainer.parentElement.classList.contains('mt-5'))
					messageContainer.parentElement.classList.remove('mt-5', 'mb-2');
				else
					messageContainer.parentElement.classList.add('mt-5', 'mb-2');
					
				document.getElementById('currentMenuControl').classList.toggle('d-none');	
				document.getElementById('closeScoreDetailContainer').classList.toggle('d-none');	
				
				document.getElementById('mapGrid').textContent = '';
				
				if(document.querySelector('header').style.opacity == 1)
					document.querySelector('header').style.opacity = 0.5;
				else
					document.querySelector('header').style.opacity = 1;
			},
			generateClickableScoreRow(score) {
				var vm = this;
				let scoreRowContainer = document.createElement('div');   
				scoreRowContainer.classList.add('row');
				
				let owner = document.createElement('div');   
				owner.classList.add('col-4', 'mb-2', 'text-center');
				owner.textContent = score.userName;
				owner.setAttribute('id', score.userName);
				owner.setAttribute('style', 'text-decoration: underline');
				owner.addEventListener('click', function(event) {
					vm.toggleScoreDetail(event.target.id);
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
			generateDataRow(key, data = null, type = 'text') {
				if(type == 'avatar') {					
					this.formData = new FormData();
					this.formData.append('token', sessionStorage.getItem('token'));
					this.formData.append('_method', 'POST');
					this.formData.append('characterName', data);
					
					const headers = { 
						'Authorization' : 'Bearer ' + sessionStorage.getItem('token')
					};
			
					const getCharacterScoreAvatar = async function(formData) {
						let response = await axios({
							method : "POST",
							baseURL: 'http://127.0.0.1:8000/api',
							url    : 'http://127.0.0.1:8000/api/getCharacterScoreAvatar',
							params : '',
							data   : formData,
							headers: headers,
						});
						return response;
					};
				
					getCharacterScoreAvatar(this.formData)
					.then(response => {
						let dataRowContainer = document.createElement('div');   
						dataRowContainer.classList.add('row', 'bg-secondary', 'mb-1');
						let dataRowAvatar = document.createElement('img');   
						dataRowAvatar.classList.add('col', 'img-fluid');
						dataRowAvatar.src = response.data.charAvatar;
						dataRowAvatar.id = 'playerAvatar';
						//document.getElementById('menuDataArea').classList.toggle('d-none');
						dataRowContainer.append(dataRowAvatar);
						document.getElementById('scoreDetailContainer').prepend(dataRowContainer);
					})
					.catch(error => {
						console.log(error);
					});
				}
				else if(type == 'scoreHeader') {
					let dataRowContainer = document.createElement('div');   
					dataRowContainer.classList.add('row');
					
					let headerTextContainer = document.createElement('div'); 
					headerTextContainer.classList.add('col-12', 'text-center');
					headerTextContainer.textContent = key;
					dataRowContainer.appendChild(headerTextContainer);
					
					document.getElementById('scoreDetailContainer').appendChild(dataRowContainer);
				}
				else if(type == 'equipmentHeader') {
					let dataRowContainer = document.createElement('div');   
					dataRowContainer.classList.add('row');
					
					let headerTextContainer = document.createElement('div'); 
					headerTextContainer.classList.add('col-12', 'text-center');
					headerTextContainer.textContent = key;
					dataRowContainer.appendChild(headerTextContainer);
					
					document.getElementById('scoreDetailContainer').appendChild(dataRowContainer);
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
					if(data != null)
						dataRowFieldData.textContent = data;
					else
						dataRowFieldData.textContent = 'none';
					dataRowContainer.appendChild(dataRowFieldData);
					
					document.getElementById('scoreDetailContainer').appendChild(dataRowContainer);
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
	#squareDetails {
		width:33vw; 
		margin-left:33vw;
		padding-top:30vh;
		margin-top:15vh;
		margin-bottom:53vh;
		height: 71vh;
		z-index: 1000;
		opacity:.7;
	}
</style>