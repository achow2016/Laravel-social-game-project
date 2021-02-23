<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
//use Illuminate\Support\Str;
//use Carbon\Carbon;
//use Mail;

use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Log;
//use DateTime;
//use DateInterval;

//use App\Mail\welcome;
//use Illuminate\Support\Facades\Mail;

//for getting character state if any
use App\Models\Character;
use App\Models\User;

class SessionController extends Controller
{
	//spa login
	public function login(Request $request) {
		
		$request->validate([
			'email' => 'required|email',
			'password' => 'required'
		]);

		$user = User::where('email', $request->email)->first();
		if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => ['Your login details are incorrect.'],
            ]);
        }
		else {
			$user->tokens()->delete();
			$token = $user->createToken('user-access-token',['server:access']);
			//return ['token' => $token->plainTextToken];
			//return ['token' => $token];
			return response(['token' => $token], 200);
		}
	}
	
	//spa logout
	public function logout(Request $request) {
		$user = User::where('id', Auth::id())->first();
		$user->tokens()->delete();
		return response('User logged out.', 200)->header('Content-Type', 'text/plain');
	}
	
	//sends user data back to spa and character existence confirmation if any saved
	public function getData(Request $request) {
		$charObj = Character::where('ownerUser', $request->user()->id)->first();
		if($charObj != null && $charObj->mapPosition != null) {
			return response(['user' => $request->user(), 'characterState' => 'true'], 200);
		}
		else
			return response(['user' => $request->user()], 200)->header('Content-Type', 'text/plain');
	}
}