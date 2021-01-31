<template>
	<div dusk="main" class="container pt-3 pb-3">
	
		<div class="row fixed-top">
			<div class="col d-flex justify-content-end">
				<span class="badge badge-pill badge-light">Light</span>
				<span class="badge badge-pill badge-dark mr-2">Dark</span>
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
								<router-link :to="{ name: 'guestbook' }"><button type="button" class="btn btn-dark w-100 mb-3">guest book</button></router-link>
								<router-link :to="{ name: 'login' }"><button type="button" class="btn btn-dark w-100">social rpg game</button></router-link>
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
				<small>&copy;Copyright 2020 Alan</small>
			</div>
		</footer>
	
	</div>
</template>
<script>
	//https://www.publicdomainpictures.net/en/view-image.php?image=23549&picture=newspapers-and-glasses
	//https://www.publicdomainpictures.net/pictures/100000/velka/night-sky-with-lonely-tree.jpg
	export default {
		data() {
			return {
				user: {
					name: 'demo'
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