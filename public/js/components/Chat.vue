<template>
    <div class="text-light">
		<header class="row">
			<div class="col text-center d-flex">		
				<div class="flex-fill w-33">
					<router-link :to="{ name: 'welcome' }"><button type="button" class="btn btn-dark flex-fill w-100">Home</button></router-link>
				</div>	
				<div class="flex-fill w-33 h-75">
					<h3 class="mt-1">Game Chat</h3>
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
		<div v-if="errorList.postText || errorList.replyText" class="col-sm-8 alert alert-warning text-center" role="alert">
			<span v-if="errorList.postText" class="text-danger">{{errorList.postText[0]}}</span>
			<span v-if="errorList.replyText" class="text-danger">{{errorList.replyText[0]}}</span>
		</div>
		
		<section class="row text-center mt-2 mb-2">
			<div class="col">
				<div v-if="!!posts" class="col-sm-12">
					<div v-for="post in posts" v-bind:id="'post' + post.post_id" class="row">
						<div class="float-none w-100">
							<div class="row">
								<div class="col">
									{{post.name}}
								</div>
								<div class="col">
									{{post.date}}
								</div>
							</div>
							<div class="row">
								<div class="col text-truncate d-inline-block" v-bind:id="'post' + post.post_id + '-text'">
									{{post.postText}}
								</div>
							</div>
							<div class="row">
								<div class="col d-flex">
									<button v-on:click="writeReply($event)" v-bind:id="'reply' + post.post_id" type="button" class="btn btn-dark flex-fill w-50">Reply</button>
									<button v-on:click="expandPost($event)" v-bind:id="'expand' + post.post_id" type="button" class="btn btn-dark flex-fill w-50">Expand</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<section class="row text-center mt-2">
			<div id="postContainer" class="col text-center mt-2">
				<div class="col">
					<form>
						<textarea id="postSpace" rows="4" class="w-100"></textarea">
						<button type="submit" @click.prevent="submitPost" class="btn btn-dark w-100">Make Post</button>
					</form>
				</div>
			</div>	
		</section>
		
		
		<div id="replyContainer" class="float-none text-center mt-2 d-none">
			<div class="col">
				<form>
					<textarea id="replySpace" rows="2" class="w-100"></textarea">
					<button type="submit" @click.prevent="submitReply" class="btn btn-dark w-100">Submit</button>
				</form>
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
				posts: '',
				replyTarget: '',
				replyBoxNode: '',
				replyId: '',
				replyText: '',
				errorList: []
			}
		},
		mounted: function() {		
			this.getPosts()
		},
		methods: {
			getPosts() {
				Csrf.getCookie().then(() => {
					User.getPosts({
						_method: 'POST',
						token: sessionStorage.getItem('token')
					}, 
						sessionStorage.getItem('token')
					)
					.then(response => {
						this.posts = response.data.posts;
					})
					.catch(error => {
						this.$router.push({name:'chat', params:{sysError:'Error, posts could not be retrieved.'}});
					});
				});
			},
			getReplies(id) {
				if(document.getElementById('expand' + id).innerText == 'Expand') {
					this.replyBoxNode = document.getElementById('post' + id).removeChild(document.getElementById('replyContainer'));
					document.getElementById('reply' + id).innerText = 'Reply'
					document.getElementById('expand' + id).click();
					return;
				}
				let idTarget = 'post' + id + '-text';
				let postTarget = document.getElementById(idTarget);
				Csrf.getCookie().then(() => {
					User.getReplies({
						_method: 'POST',
						token: sessionStorage.getItem('token'),
						postId: id
					},
						sessionStorage.getItem('token')
					)
					.then(response => {
						let replies = response.data.replies;
						let replyHeader = document.createElement('div');
						replyHeader.className = 'col bg-dark text-center d-inline-block';
						let headerText = 'Replies';
						let headerTextNode = document.createTextNode(headerText);
						replyHeader.appendChild(headerTextNode);
						postTarget.appendChild(replyHeader);
						
						for(let i = 0; i < replies.length; i++) {
							let replyContainer = document.createElement('div');
							
							if(i % 2 == 0) {
								replyContainer.className = 'col bg-secondary text-center d-inline-block';
							} 
							else {
								replyContainer.className = 'col bg-dark text-center d-inline-block';
							}

							let replyText = replies[i].date + ' ' + replies[i].name + 
								' says: ' + replies[i].postText;
							let replyTextNode = document.createTextNode(replyText);
							replyContainer.appendChild(replyTextNode);
							postTarget.appendChild(replyContainer);
						}
					})
					.catch(error => {
						this.$router.push({name:'chat', params:{sysError:'Error, replies to posts could not be retrieved.'}});
					});
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
				})
				.catch(error => {
					this.$router.push({name:'login', params:{navError:'Error, logout could not be performed.'}});
				});
			},
			writeReply(event){
				let idText = event.target.id;
				this.replyId = idText.replace(/[^0-9]/g,'');
				
				if(event.target.innerText == 'Close') {
					this.replyBoxNode = document.getElementById('post' + this.replyId).removeChild(document.getElementById('replyContainer'));
					event.target.innerText = 'Reply'
				}
				else {	
					if(this.replyBoxNode == '')
						//if not displayed yet, appends to parent post
						if(document.getElementById('replyContainer').classList.contains('d-none'))
							document.getElementById('post' + this.replyId).appendChild(document.getElementById('replyContainer'));
						//if displayed on a different post, removed from the parent and move to target
						else {
							this.replyBoxNode = document.getElementById('replyContainer').parentElement.removeChild(document.getElementById('replyContainer'));
							document.getElementById('post' + this.replyId).appendChild(this.replyBoxNode);
							let buttons = document.getElementsByTagName('button');
							for(let i = 0; i < buttons.length; i++) {
								if(buttons[i].textContent == 'Close')
									buttons[i].textContent = 'Reply';
							}
						}
					else {
						document.getElementById('post' + this.replyId).appendChild(this.replyBoxNode);
						let buttons = document.getElementsByTagName('button');
						for(let i = 0; i < buttons.length; i++) {
							if(buttons[i].textContent == 'Close')
								buttons[i].textContent = 'Reply';
						}
					}
					event.target.innerText = 'Close';
					document.getElementById('replyContainer').className = 'col text-center mt-2';
					document.getElementById('replySpace').setAttribute('rows', '4');
					document.getElementById('replySpace').focus();
				}
			},
			submitReply() {
				let replySpace = document.getElementById('replySpace');
				let idTarget = 'post' + this.replyId + '-text';
				let postTarget = document.getElementById(idTarget);
				Csrf.getCookie().then(() => {
					User.makePostReply({
						_method: 'POST',
						token: sessionStorage.getItem('token'),
						postId: this.replyId,
						replyText: replySpace.value
					},
						sessionStorage.getItem('token')
					)
					.then(response => {
						replySpace.value = '';
						let cleaningTarget = document.getElementById('post' + this.replyId + '-text');
						while(cleaningTarget.childElementCount >= 1) {  
							cleaningTarget.removeChild(postTarget.childNodes[1]);
						}
						this.getReplies(this.replyId);
						this.errorList = [];
					})	
					.catch(error => {
						if(error.response.status == 422)
							this.errorList = error.response.data.errors;
					});
				});
			},
			submitPost() {
				let postSpace = document.getElementById('postSpace');
				Csrf.getCookie().then(() => {
					User.makePost({
						_method: 'POST',
						token: sessionStorage.getItem('token'),
						postText: postSpace.value
					},
						sessionStorage.getItem('token')
					)
					.then(response => {
						this.getPosts();
						postSpace.value = '';
						this.errorList = [];
					})
					.catch(error => {
						if(error.response.status == 422)
							this.errorList = error.response.data.errors;
					});
				});
			},
			expandPost(event) {
				let idText = event.target.id;
				let postId = idText.replace(/[^0-9]/g,'');
				let idTarget = 'post' + postId + '-text';
				let postTarget = document.getElementById(idTarget);
				if(event.target.innerText == 'Expand') {
					postTarget.className = 'col d-inline-block';
					event.target.innerText = 'Collapse';
					this.getReplies(postId);
				}	
				else {
					postTarget.className = 'col text-truncate d-inline-block';
					let cleaningTarget = document.getElementById('post' + postId + '-text');
					while(cleaningTarget.childElementCount >= 1) {  
						cleaningTarget.removeChild(postTarget.childNodes[1]);
					}
					event.target.innerText = 'Expand';
				}	
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
</style>