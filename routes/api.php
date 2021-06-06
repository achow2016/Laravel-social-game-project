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
	Route::post('/getCharacterStatus', 'App\Http\Controllers\CharacterController@getCharacterStatus');
	Route::post('/getCharacterInventory', 'App\Http\Controllers\CharacterController@getCharacterInventory');
	Route::post('/getCharacterExistenceStatus', 'App\Http\Controllers\CharacterController@getCharacterStatus');
	Route::post('/getCharacterBattleStatus', 'App\Http\Controllers\CharacterController@getCharacterBattleStatus');
	Route::post('/generateMap', 'App\Http\Controllers\MapController@generateMap');
	Route::post('/generateEnemies', 'App\Http\Controllers\EnemyController@generateEnemies');
	Route::post('/getEnemies', 'App\Http\Controllers\EnemyController@getEnemies');
	Route::post('/inspectEnemies', 'App\Http\Controllers\EnemyController@inspectEnemies');
	Route::post('/inspectBattleEnemy', 'App\Http\Controllers\EnemyController@inspectBattleEnemy');
	Route::post('/getMap', 'App\Http\Controllers\MapController@getMap');
	Route::post('/getAvatar', 'App\Http\Controllers\CharacterController@getAvatar');
	Route::post('/useItem', 'App\Http\Controllers\CharacterController@useItem');
	Route::post('/lootEnemy', 'App\Http\Controllers\CharacterController@lootEnemy');
	Route::post('/moveCharacter', 'App\Http\Controllers\MapController@moveCharacter');
	//Route::post('/fightEnemy', 'App\Http\Controllers\CharacterController@fightEnemy');
	Route::post('/switchFight', 'App\Http\Controllers\CharacterController@switchFight');
	Route::post('/startFight', 'App\Http\Controllers\CharacterController@startFight');
	Route::post('/meleeEnemy', 'App\Http\Controllers\CharacterController@meleeEnemy'); //within fight component
	Route::post('/getTurnList', 'App\Http\Controllers\CharacterController@getTurnList'); //gets turn listing from character
	Route::post('/getGameState', 'App\Http\Controllers\CharacterController@getGameState'); //get game state data using character
	Route::post('/gameEnemyTurnDecision', 'App\Http\Controllers\EnemyController@gameEnemyTurnDecision');
	Route::post('/nextLevel', 'App\Http\Controllers\CharacterController@nextLevel');
	Route::post('/quitGame', 'App\Http\Controllers\CharacterController@quitGame');
	//game account
	Route::post('/getUserProfile', 'App\Http\Controllers\RegistrationController@getUserProfile');
	Route::post('/updateProfileVideo', 'App\Http\Controllers\RegistrationController@updateProfileVideo');
	Route::post('/updateProfileImage', 'App\Http\Controllers\RegistrationController@updateProfileImage');
	Route::post('/updateName', 'App\Http\Controllers\RegistrationController@updateName');
	Route::post('/updateEmail', 'App\Http\Controllers\RegistrationController@updateEmail');
	Route::post('/updatePassword', 'App\Http\Controllers\RegistrationController@updatePassword');
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