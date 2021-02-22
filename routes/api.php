<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => 'auth:sanctum'], function() {
	Route::post('/getData', 'App\Http\Controllers\SessionController@getData');
	Route::post('/logout', 'App\Http\Controllers\SessionController@logout');
	//chat
	Route::post('/getPosts', 'App\Http\Controllers\ChatController@getPosts');
	Route::post('/getReplies', 'App\Http\Controllers\ChatController@getReplies');
	Route::post('/makePostReply', 'App\Http\Controllers\ChatController@makePostReply');
	Route::post('/makePost', 'App\Http\Controllers\ChatController@makePost');
	//game
	Route::post('/createCharacter', 'App\Http\Controllers\CharacterController@createCharacter');
	Route::post('/generateMap', 'App\Http\Controllers\MapController@generateMap');
	Route::post('/getMap', 'App\Http\Controllers\MapController@getMap');
	Route::post('/moveCharacter', 'App\Http\Controllers\MapController@moveCharacter');
	Route::post('/getUserProfile', 'App\Http\Controllers\RegistrationController@getUserProfile');
	Route::post('/updateProfileVideo', 'App\Http\Controllers\RegistrationController@updateProfileVideo');
	Route::post('/updateProfileImage', 'App\Http\Controllers\RegistrationController@updateProfileImage');
	//store
	Route::post('/getStoreItems', 'App\Http\Controllers\GameStoreController@getStoreItems');
	Route::post('/addCartItem', 'App\Http\Controllers\GameStoreController@addCartItem');
	Route::post('/getCartItems', 'App\Http\Controllers\GameStoreController@getCartItems');
	Route::post('/deleteCartItem', 'App\Http\Controllers\GameStoreController@deleteCartItem');
	Route::post('/updateCartItem', 'App\Http\Controllers\GameStoreController@updateCartItem');
	Route::post('/checkout', 'App\Http\Controllers\GameStoreController@checkout');
	
});

Route::middleware('auth:sanctum')->get('/welcome', function (Request $request) {
    return $request->user();
});

//account
Route::post('/resetPassword', 'App\Http\Controllers\RegistrationController@generateResetPasswordLink');
Route::post('register', 'App\Http\Controllers\RegistrationController@register');
Route::post('login', 'App\Http\Controllers\SessionController@login');
Route::get('logout', 'App\Http\Controllers\SessionController@logout');

//guest users
Route::get('recordGuest', 'App\Http\Controllers\GuestbookController@recordGuest');
Route::get('getGuestbookNotes', 'App\Http\Controllers\GuestbookController@getGuestbookNotes');
Route::post('addGuestbookNote', 'App\Http\Controllers\GuestbookController@addGuestbookNote');