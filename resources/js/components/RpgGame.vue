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
				<h5>Rpg Game</h5>
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
		mounted() { 
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
						row.classList.add('row');
						row.setAttribute('id', 'row' + i);
						document.getElementById('mapGrid').appendChild(row);
						for (let j = 0; j < 8; j++) {
							let element = document.createElement('div');
							element.classList.add('col');
							element.setAttribute('id', 'row' + i + 'col' + j);
							if(this.mapData[i][j].terrain == 'grass')
								element.classList.add('bg-success');
							else
								element.classList.add('bg-primary');
							
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
</style>