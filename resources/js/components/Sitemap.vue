<template>
	<div dusk="main" class="vh-100 container pt-3 pb-3">
	
		<div class="row fixed-top">
			<div id="themeOptions" class="bg-secondary col-11 d-flex justify-content-end invisible">
				<a href="#" v-on:click="toggleToLight()" class="badge badge-pill badge-light mr-4">
					<b-icon-brightness-high-fill></b-icon-brightness-high-fill>
				</a>
				<a href="#" v-on:click="toggleToDefault()" class="badge badge-pill badge-secondary mr-4">
					<b-icon-brightness-low-fill></b-icon-brightness-low-fill>
				</a>
				<a href="#" v-on:click="toggleToDark()" class="badge badge-pill badge-dark mr-4">
					<b-icon-brightness-low></b-icon-brightness-low>
				</a>
			</div>
			<div class="col-1 d-flex justify-content-end">
				<a href="#" v-on:click="showThemeToggle()" class="badge badge-pill badge-light">&#9680;</a>
			</div>
		</div>
		
		<header v-bind:style="topSection" class="row p-3 m-3">
			<div class="col">
				<div class="row text-center">
					<div class="col">
						<h1>Alan's Website</h1>
					</div>	
				</div>	
				<div class="row text-center">
					<div class="col">
						<h5>Welcome to my website!</h5>
					</div>	
				</div>
			</div>
		</header>
		
		<div id="wideScreen">
			<nav v-bind:style="topNav" class="row p-3 m-3">
				<div class="col-12 mx-auto">
					<div class="row">
						<div class="col text-center">
							<h5>Works</h5>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<div class="mx-auto w-30">
								<img src="/img/guestbook_snapshot.jpg" id="guestbookSnapshot" alt="Guestbook Snapshot" style="width:300px;height:300px" class="mx-auto d-block mb-3">
								<router-link :to="{ name: 'guestbook' }"><button type="button" class="btn btn-dark w-100 mb-3">guest book</button></router-link>
							</div>
						</div>
						<div class="col-6">
							<div class="mx-auto w-30">
								<img src="/img/game_snapshot.jpg" id="gameSnapshot" alt="Game Snapshot" style="width:300px;height:300px" class="mx-auto d-block mb-3">
								<router-link :to="{ name: 'login' }"><button type="button" class="btn btn-dark w-100">social rpg game</button></router-link>
							</div>
						</div>
					</div>
				</div>		
			</nav>			
		</div>
	
		<div id="smallScreen">
			<nav v-bind:style="topNav" class="row p-3 m-3">
				<div class="col-9 mx-auto">
					<div class="row">
						<div class="col text-center">
							<h5>Works</h5>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="mx-auto w-30">
								<router-link :to="{ name: 'guestbook' }"><button type="button" class="btn btn-dark w-100 mb-3">guest book<span class="badge badge-secondary">New</span></button></router-link>
								<router-link :to="{ name: 'login' }"><button type="button" class="btn btn-dark w-100">social rpg game<span class="badge badge-secondary">New</span></button></router-link>
							</div>
						</div>
					</div>
				</div>
			</nav>
		</div>
		
		<article v-bind:style="middleArticle" class="row p-3 m-3">
			<div class="col text-center">
				<p>I am using this website to show work from my github!</p>
			</div>
		</article>
		<footer v-bind:style="bottomFooter" class="row p-3 m-3">
			<div class="col text-center">
				<small>&copy;Copyright 2020</small>
				<div v-for="data in personalInfo">
					<small>By: {{data[0].name}}</small>
					<br>
					<small>Made In: {{data[0].country}}</small>
				</div>
			</div>
		</footer>
	
	</div>
</template>
<script>
	//https://www.publicdomainpictures.net/en/view-image.php?image=23549&picture=newspapers-and-glasses
	//https://www.publicdomainpictures.net/pictures/100000/velka/night-sky-with-lonely-tree.jpg
	
	import personalInfo from '../../../public/json/PersonalInfo.json';
	export default {
		data() {
			return {
				personalInfo: personalInfo,
				user: {
					name: 'demo',
					
				},
				topSection: {
					color: 'white',
					'background-color': 'black',
					opacity: .7	
				},
				topNav: {
					color: 'white',
					'background-color': 'black',
					opacity: .7
				},
				middleArticle: {
					color: 'white',
					'background-color': 'black',
					opacity: .7	
				},
				bottomFooter: {
					color: 'white',
					'background-color': 'black',
					opacity: .7	
				}
			}
		},
		mounted() {
		},
		created() {
			let responseData = this.$route.params;
			console.log(responseData);
		},
		methods: {
			toggleToDefault() {
				var container = document.querySelector('.container');
				if(!container.classList.contains('darkMode')) {
					container.classList.remove('lightMode');
				}
				else
					container.classList.remove('darkMode');
			},
			toggleToDark() {
				var container = document.querySelector('.container');
				if(!container.classList.contains('darkMode')) {
					container.classList.toggle('darkMode');
					container.classList.remove('lightMode');
				}
			},
			toggleToLight() {
				var container = document.querySelector('.container');
				if(!container.classList.contains('lightMode')) {				
					container.classList.toggle('lightMode');
					container.classList.remove('darkMode');
				}
			},
			showThemeToggle() {
				var container = document.querySelector('#themeOptions');				
				container.classList.toggle('invisible');
			}			
		}	
	}
</script>
<style>
	body {
		background-image: url('/img/news_glasses.jpg')
	}
	
	

</style>
<style scoped>
	
	.lightMode {
		background-color: white;
		opacity: .7;
	}
	
	.darkMode {
		background-color: black;
		opacity: .7;
	}

	div button:first-child {
		border: 1px solid white;
	}
	
	#wideScreen {
		display: none;
	}
	
	@media (max-width: 575.98px) { 
		#wideScreen {
			display: none;
		}
		#smallScreen {
			display: block;
		}
	}

	@media (min-width: 576px) and (max-width: 767.98px) and (orientation: landscape){ 
		#wideScreen {
			display: block;
		}
		#smallScreen {
			display: none;
		}	 
	}

	@media (min-width: 768px) {
		#wideScreen {
			display: block;
		}
		#smallScreen {
			display: none;
		}
	}

</style>