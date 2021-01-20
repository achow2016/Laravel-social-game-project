<template>
    <div class="text-light">
		<div class="row mx-auto">
			<div class="col">
				
				<header class="row fixed-top">
					<div class="col text-center d-flex">		
						<div class="flex-fill w-33">
							<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
						</div>	
						<div class="flex-fill w-33 h-75">
							<h3 class="mt-1">Game Store</h3>
						</div>	
						<div class="flex-fill w-33">
							<button v-on:click="logout" type="button" class="btn btn-dark flex-fill w-100">Logout</button>
						</div>	
					</div>
				</header>
				
				<div v-if="!!sysError" class="row text-center mt-2 mb-2">
					<div class="col-sm-8 alert alert-warning" role="alert">
						<span class="text-danger">{{sysError}}</span>
					</div>
				</div>
				<div v-if="errorList.addText || errorList.removeText" class="col-sm-8 alert alert-warning text-center" role="alert">
					<span v-if="errorList.addText" class="text-danger">{{errorList.addText[0]}}</span>
					<span v-if="errorList.removeText" class="text-danger">{{errorList.removeText[0]}}</span>
				</div>
				
				<section class="row text-center mt-5 mb-2">
					<div class="col" id="itemContainer">
						<div v-if="!!items" class="col-sm-12">
							<div v-for="item in items" v-bind:id="'item' + item.id" class="row bg-secondary mb-3">
								<div class="float-none w-100">
									<div class="row">
										<div class="col">
											{{item.name}}
										</div>
										<div class="col">
											Cost each: {{item.cost}}
										</div>
									</div>
									<div class="row">
										<div class="col-8">
											<p v-if="item.quantity < 0">Stock: Unlimited</p>
											<p v-else="item.quantity < 0">Stock: {{item.quantity}}</p>
										</div>
										<div class="col-4">
											<input type="text" v-bind:id="'itemQuantity' + item.id" class="form-control" placeholder="quantity">
										</div>
									</div>
									<div class="row">
										<div class="col text-truncate d-inline-block" v-bind:id="'item' + item.id + '-text'">
											{{item.itemDescription}}
										</div>
									</div>
									<div class="row">
										<div class="col d-flex flex-row-reverse">
											<button v-on:click="addToCart($event)" v-bind:id="'item' + item.id" type="button" class="btn btn-dark w-25">Add</button>
											<button v-on:click="expandDescription($event)" v-bind:id="'expand' + item.id" type="button" class="btn btn-dark w-25">Expand</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				
				
				<div id="cartContainer" class="float-none text-center mt-2 d-none">
					<div class="col">
						<div v-if="!!cartItems">
							<div v-for="cartItem in cartItems" v-bind:id="'cartItem' + cartItem.id" class="row">
								<div class="float-none w-100 bg-secondary mb-2">
									<div class="row">
										<div class="col">
											{{cartItem.name}}
										</div>
										<div class="col text-right">
											Item Total: {{cartItem.price}}
										</div>
									</div>
									<div class="row">
										<div class="col text-right">
											Item Quantity: {{cartItem.quantity}}
										</div>
									</div>
									<div class="row">
										<div class="col-4">
											<button v-bind:id="'deleteCartItem' + cartItem.id" v-on:click="deleteCartItem($event);refreshCart();" type="button" class="mb-1 btn btn-dark w-100">Delete</button>
										</div>
										<div class="col-4">
											<input type="text" v-bind:id="'cartItemQuantity' + cartItem.id" class="form-control" placeholder="quantity">
										</div>	
										<div class="col-4">
											<button v-bind:id="'updateCartItem' + cartItem.id" v-on:click="updateCartItem($event);refreshCart();" type="button" class="mb-1 btn btn-dark w-100">Update</button>
										</div>											
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col text-right bg-secondary mt-1">
								Cart Subtotal: {{cartTotal}}
							</div>
						</div>
					</div>
				</div>
				
				<div class="row fixed-bottom">
					<div class="col">
						<div class="row d-flex">
							<button id="cartButton" v-on:click="openCart();refreshCart();" type="button" class="ml-3 mb-1 rounded-circle btn btn-dark w-25 border border-success">Cart</button>
						</div>	
					</div>				
				</div>			
			</div>	
		</div>
    </div>
</template>
<script>
	import User from '../apis/User';
	import Csrf from '../apis/Csrf';
	
	export default {
		props : ['title','message','error'],
		data() {
			return {
				items: '',
				cartItems: '',
				cartTotal: '',
				errorList: []
			}
		},
		mounted: function() {		
			this.getStoreItems();
		},
		methods: {
			updateCartItem(event) {
				let idText = event.target.id;
				let itemId = idText.replace(/[^0-9]/g,'');
				let quantity = document.getElementById('cartItemQuantity' + itemId).value;
				Csrf.getCookie().then(() => {
					User.updateCartItem({
						_method: 'POST',
						token: sessionStorage.getItem('token'),
						itemId: itemId,
						quantity: quantity
						
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						console.log(response);
						document.getElementById('cartItemQuantity' + itemId).value = '';
					})
					.catch(error => {
						console.log(error);
					});
				});
			},
			deleteCartItem(event) {
				let idText = event.target.id;
				let itemId = idText.replace(/[^0-9]/g,'');
				Csrf.getCookie().then(() => {
					User.deleteCartItem({
						_method: 'POST',
						token: sessionStorage.getItem('token'),
						itemId: itemId
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						console.log(response);
					})
					.catch(error => {
						console.log(error);
					});
				});
			},
			getStoreItems() {
				Csrf.getCookie().then(() => {
					User.getStoreItems({
						_method: 'POST',
						token: sessionStorage.getItem('token')
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						this.items = response.data.cashItems;
						console.log(response);
					})
					.catch(error => {
						console.log(error);
					});
				});
			},
			openCart() {
				document.getElementById('cartContainer').classList.toggle('d-none');
				document.getElementById('itemContainer').classList.toggle('d-none');
				let cartButton = document.getElementById('cartButton');
				if(cartButton.innerText == 'Cart')
					cartButton.innerText = 'Close';
				else	
					cartButton.innerText = 'Cart';
			},
			refreshCart() {
				Csrf.getCookie().then(() => {
					User.getCartItems({
						_method: 'POST',
						token: sessionStorage.getItem('token'),
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						this.cartItems = response.data.cartItems;
						this.cartTotal = response.data.cartTotal;
						console.log(response);
						
					})
					.catch(error => {
						console.log(error);
					});
				});
			},
			addToCart(event) {
				let idText = event.target.id;
				let itemId = idText.replace(/[^0-9]/g,'');
				let quantity = document.getElementById('itemQuantity' + itemId).value;
				Csrf.getCookie().then(() => {
					User.addCartItem({
						_method: 'POST',
						token: sessionStorage.getItem('token'),
						itemId: itemId,
						quantity: quantity
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						console.log(response);
						document.getElementById('itemQuantity' + itemId).value = '';
					})
					.catch(error => {
						console.log(error);
					});
				});
			},
			checkout() {
				Csrf.getCookie().then(() => {
					User.checkout({
						_method: 'POST',
						token: sessionStorage.getItem('token'),
						cartItems: this.cartItems
					},
						sessionStorage.getItem('token')
					)
					.then(response => {
					})	
					.catch(error => {
						console.log(error);
					});
				});
			},
			expandDescription(event) {
				let idText = event.target.id;
				let itemId = idText.replace(/[^0-9]/g,'');
				let idTarget = 'item' + itemId + '-text';
				let itemTarget = document.getElementById(idTarget);
				if(event.target.innerText == 'Expand') {
					itemTarget.className = 'col d-inline-block';
					event.target.innerText = 'Collapse';
				}	
				else {
					itemTarget.className = 'col text-truncate d-inline-block';
					let cleaningTarget = document.getElementById('item' + itemId + '-text');
					while(cleaningTarget.childElementCount >= 1) {  
						cleaningTarget.removeChild(itemTarget.childNodes[1]);
					}
					event.target.innerText = 'Expand';
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
				})
				.catch(error => {
					this.$router.push({name:'login', params:{navError:'Error, logout could not be performed.'}});
				});
			}
		},
		computed: {
			sysError (){
				return this.$route.params.sysError;
			}
		}
	}
</script>
<style scoped>
	.bg-secondary {
		opacity: 0.85;
	}
</style>