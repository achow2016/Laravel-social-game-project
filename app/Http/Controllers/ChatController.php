<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
use App\Models\Post;
use App\Models\User;
use App\Models\Reply;

use DateTime;

class ChatController extends Controller {

	public function getPosts() 
	{
		$posts = Post::all();
		return response(['posts' => $posts], 200);
	}
	
	public function getReplies(Request $request) 
	{
		$targetPostId = $request->input('postId');
		$replies = Reply::where('target_post_id', $targetPostId)->get();
		return response(['replies' => $replies], 200);
	}
	
	public function makePostReply(Request $request)
	{
		$request->validate([
			'postId' => 'required',
			'replyText' => 'required'		
		]);
		
		$targetPostId = $request->input('postId');
		$replyText = $request->input('replyText');
		$name = $request->user()->name;
		$user = User::where('name', $name)->first();
		$date = new DateTime("now");

		$reply = new Reply();
		$reply->setAttribute('user_id', $user->id);
		$reply->setAttribute('target_post_id', $targetPostId);
		$reply->setAttribute('name', $name);
		$reply->setAttribute('postText', $replyText);
		$reply->setAttribute('date', $date);
		
		$post = Post::where('post_id', $targetPostId)->first();
		$post->replies()->save($reply);
		$post->save();	
		//return redirect('/rpgGame/textBoard/confirmPost'); 
	}
	
	public function makePost(Request $request)
	{
		$request->validate([
			'postText' => 'required'		
		]);
		
		$name = $request->user()->name;
		$user = User::where('name', $name)->first();
		$postText = $request->input('postText');
		$date = new DateTime("now");

		$post = new Post();
		$post->setAttribute('user_id', $user->id);
		$post->setAttribute('name', $name);
		$post->setAttribute('postText', $postText);
		$post->setAttribute('date', $date);

		$post->save();	
		//return redirect('/rpgGame/textBoard/confirmPost'); 
	}
}
?>