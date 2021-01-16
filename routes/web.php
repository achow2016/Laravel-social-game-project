<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\RegistrationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Route::post('api/checkAccess', 'App\Http\Controllers\SessionController@checkAccess');
/*
Route::get('/login', function (){ return redirect('/loginForm');})->name('login');

Route::group(['middleware' => 'auth:sanctum'], function() {
	Route::post('/getData', 'App\Http\Controllers\SessionController@getData');
	Route::post('/logout', 'App\Http\Controllers\SessionController@logout');
});

Route::post('register', 'App\Http\Controllers\RegistrationController@register');

*/
Route::get('/processPasswordReset', 'App\Http\Controllers\RegistrationController@processPasswordReset');

Route::get('/{any}', function(){
		return view('vueapp');
})->where('any', '.*');




