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
			<div class="col">
				<h5><!--level number--></h5>
			</div>
		</div>
		
		<div class="row mt-5 mb-5" id="gridArea">
			<div class="col">		
				<div>
					<div id="mapGrid" class="col text-center">
						Generating map.
					</div>					
				</div>
			</div>
		</div>
		
		<div class="row mt-5 mb-5" id="controlArea">
			<div class="col-6">		
				<div id="directionGrid" class="text-center">
					<div class="row mb-4 controllerRow">
						<div class="col-4"><b-icon icon="arrow-up-left-circle"></b-icon></div>
						<div class="col-4"><b-icon icon="arrow-up-circle"></b-icon></div>
						<div class="col-4"><b-icon icon="arrow-up-right-circle"></b-icon></div>
					</div>
					<div class="row mb-4 controllerRow">
						<div class="col-4"><b-icon icon="arrow-left-circle"></b-icon></div>
						<div class="col-4"><b-icon icon="app"></b-icon></div>
						<div class="col-4"><b-icon icon="arrow-right-circle"></b-icon></div>
					</div>
					<div class="row mb-4 controllerRow">
						<div class="col-4"><b-icon icon="arrow-down-left-circle"></b-icon></div>
						<div class="col-4"><b-icon icon="arrow-down-circle"></b-icon></div>
						<div class="col-4"><b-icon icon="arrow-down-right-circle"></b-icon></div>
					</div>
				</div>				
			</div>
			<div id="actionGrid" class="col-6">
				<div class="row-9 mb-4 actionRow d-flex justify-content-center">Inspect</div>
				<div class="row-9 mb-4 actionRow d-flex justify-content-center">Fight</div>
				<div class="row-9 mb-4 actionRow d-flex justify-content-center">Skill</div>
				<div class="row-9 mb-4 actionRow d-flex justify-content-center">Loot</div>
			</div>
		</div>
		
		<div class="row fixed-bottom">
			<div class="col text-center d-flex">		
				<div class="flex-fill w-33">
					<button v-on:click="openInventory" type="button" class="btn btn-dark flex-fill w-100">Inventory</button>
				</div>	
				<div class="flex-fill w-33 h-75">
					<button v-on:click="openStatus" type="button" class="btn btn-dark flex-fill w-100">Status</button>
				</div>	
				<div class="flex-fill w-33">
					<button v-on:click="openGameMenu" type="button" class="btn btn-dark flex-fill w-100">Menu</button>
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
				mapData: ''
			}
		},
		beforeMount() { 
			User.getMap({
					_method: 'POST', token: sessionStorage.getItem('token')
				}, 
					sessionStorage.getItem('token')
				)
				.then((response) => {
					console.log(JSON.parse(response.data.mapData));
					this.mapData = JSON.parse(response.data.mapData);
					
					document.getElementById('mapGrid').innerHTML = ""; 
					for (let i = 0; i < 8; i++) {
						let row = document.createElement('div');
						row.classList.add('row', 'mapGridRow');
						row.setAttribute('id', 'row' + i);
						document.getElementById('mapGrid').appendChild(row);
						for (let j = 0; j < 8; j++) {
							let element = document.createElement('div');
							element.classList.add('col');
							element.setAttribute('id', 'row' + i + 'col' + j);
							
							if(this.mapData[i][j].terrain == 'grass')
								element.classList.add('gameGridSquare', 'bg-success', 'pt-2', 'pb-2');
							else
								element.classList.add('gameGridSquare', 'bg-primary', 'pt-2', 'pb-2');
							
							if(this.mapData[i][j].treeCover == true) {
								let treeMarker = document.createTextNode('T');
								element.appendChild(treeMarker);
								element.classList.add('tree');
							}
							else {
								let openMarker = document.createTextNode('-');
								element.appendChild(openMarker);
								element.classList.add('open');
							}
							document.getElementById('row' + i).appendChild(element);
						}
					}
						
					
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
			moveCharacter(event) {

			},
			openInventory() {

			},
			openStatus() {

			},
			openGameMenu() {

			},
			inspect() {

			},
			saveAndQuit() {

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