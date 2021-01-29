<template>
	<div dusk="main">
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
		<nav v-bind:style="topNav" class="row p-3 m-3">
			<div class="col">
				<div class="row">
					<div class="col text-center">
						<h5>Works</h5>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="mx-auto w-30">
							<router-link :to="{ name: 'guestbook' }"><button type="button" class="btn btn-dark w-100">guest book</button></router-link>
							<router-link :to="{ name: 'login' }"><button type="button" class="btn btn-dark w-100">social rpg game</button></router-link>
						</div>
					</div>
				</div>
			</div>
		</nav>
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
					opacity: .5	
				},
				topNav: {
					color: 'white',
					'background-color': 'black',
					opacity: .5	
				},
				middleArticle: {
					color: 'white',
					'background-color': 'black',
					opacity: .5	
				},
				bottomFooter: {
					color: 'white',
					'background-color': 'black',
					opacity: .5	
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
	div button:first-child {
		border: 1px solid white;
	}
</style>