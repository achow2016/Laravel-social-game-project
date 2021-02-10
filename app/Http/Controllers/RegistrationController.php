<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\ProfileVideo;
use App\Models\ProfileImage;
use DateTime;
use DateInterval;

use App\Mail\Welcome;
use App\Mail\PasswordReset;
use App\Mail\PasswordResetConfirmation;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
	//spa register
	public function register(Request $request) {
		$request->validate([
			'name' => 'required',
			'email' => 'required|email|unique:App\Models\User',
			'password' => 'required|confirmed'
		]);
		
		Mail::to($request->email)->send(new Welcome('Welcome to rpg game!'));		
		
		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => $request->password //hashed in user model
		]);
		
		if(count(Mail::failures()) > 0) {
			return ['token' => $user->createToken('user-access-token',['server:access'])->plainTextToken, 'message' => 'Registration successful, welcome email failed to be sent.'];
		} 
		else {
			return ['token' => $user->createToken('user-access-token',['server:access'])->plainTextToken, 'message' => 'registration successful!'];
		}
	}
	
	//generates a random token in db and calls send reset email function
	public function generateResetPasswordLink(Request $request) {
		$this->validate(request(), [
			'email' => 'required|email'
		]);
		
		$email = $request->input('email');
		
		$user = User::where('email', $email)->first();
		if(!$user) {
			$message = 'Error, user does not exist!';
			return redirect('/resetPassword?message=' . urlencode($message));			
		}
		
		//token work
		DB::table('rpguserreset')->insert([
			'email' => $email,
			'token' => Str::random(60),
			'created_at' => Carbon::now()
		]);

		$tokenData = DB::table('rpguserreset')->where('email', $request->email)->first();

		//calls mailing function in this class
		if ($this->sendResetEmail($request->email, $tokenData->token)) {
			$message = 'Success, reset email sent to email address!';
			return redirect('/resetPassword?message=' . urlencode($message)); 
		} 
		else {
			$message = 'Failed, internal error occurred. Please contact administrator!';
			return redirect('/resetPassword?message=' . urlencode($message)); 
		}
	}
	
	//sends password reset email with token
	private function sendResetEmail($email, $token)
	{	
		$user = User::where('email', $email)->first();

		//Generates the password reset link and token
		$text = "127.0.0.1:8000/processPasswordReset?token=" . $token . "&email=" . urlencode($email);
		
		Mail::to($email)->send(new PasswordReset('Click on this link to reset your password: ' . $text));
		
		if(count(Mail::failures()) > 0) {
			return false;		 
		} 
		else {
			return true;
		}
	}

	//processes visit to reset password route from email
	public function processPasswordReset(Request $request) {
		$validator = Validator::make($request->all(), [
			'email' => 'required',
			'token' => 'required' 
		]);
		if($validator->fails()) {
			$message = 'Failed, please send password request again.';
			return redirect('/resetPassword?message=' . urlencode($message)); 
		}

		//Validates the token
		//Redirects the user back to the password reset request form if the token is invalid
		$tokenData = DB::table('rpguserreset')->where('token', $request->token)->first();
		if(!$tokenData) {
			$message = 'Failed, your reset token is invalid.';
			return redirect('/resetPassword?message=' . urlencode($message)); 		
		}
		
		//Redirects the user back if the email is invalid
		$user = User::where('email', $request->email)->first();
		if(!$user) {
			$message = 'Failed, email not found!';
			return redirect('/resetPassword?message=' . urlencode($message));		
		}
		
		//update the new password, hashed by model function already
		$tempPassword = Str::random(10);
		$user->password = $tempPassword;
		$user->save();

		//Deletes the token
		DB::table('rpguserreset')->where('email', $request->email)->delete();
		
		Mail::to($request->email)->send(new PasswordResetConfirmation('Password reset complete, your new password is: ' . $tempPassword));
		
		if(count(Mail::failures()) > 0) {
			$message = 'Error, new password not sent please retry or contact administrator.';
			return redirect('/resetPassword?message=' . urlencode($message));	 
		} 
		else {
			$message = 'Success, Check your email for your new password.';
			return redirect('/resetPassword?message=' . urlencode($message));
		}	
	}
	
	//spa get account profile
	public function getUserProfile(Request $request) {
		try {
			$userData = User::where('name', $request->user()->name)->first();			
			$userData['profile_image'] = $userData->profileImage;
			$userData['profile_video'] = $userData->profileVideo;
			return response([
				'userData' => $userData
			],200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Profile could not be retrieved. Please report to admin.'], 422);
		}
	}

	//spa add a profile video
	public function updateProfileVideo(Request $request) 
	{

		Log::debug($request->all());
		
		$request->validate([
			//'profileVideo' => 'required|mimes:mp4'
		]);
		
		try {
			//gets user, to update record with an profile video
			$username = $request->user()->name;
			$myUser = User::where('name', $username)->first();
			//processes and stores
			$file = $request->file('profileVideo');			
			//$file = file_get_contents($request->profileVideo->path());
			//$file = $_POST['profileVideo'];			
			//getting timestamp
			$timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
			$name = $timestamp. '-' .$file->getClientOriginalName();
			$file->move(public_path().'/vid/rpgGame/', $name);
			$vidPath = url('/vid/rpgGame/' . $name);
			$profileVideo = new ProfileVideo();
			$profileVideo->user_id = $myUser->id;
			$profileVideo->profile_video = $vidPath;
			$myUser->profileVideo()->save($profileVideo);
			$myUser['profile_image'] = $myUser->profileImage;
			$myUser['profile_video'] = $myUser->profileVideo;
			$myUser->save();
			return response(['userData' => $myUser], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Profile video could not be processed. Please report to admin.'], 422);
		}
	}

//spa add a profile video
	public function updateProfileImage(Request $request) 
	{

		Log::debug($request->all());
		
		$request->validate([
			//'profileVideo' => 'required|mimes:mp4'
		]);
		
		try {
			//gets user, to update record with an avatar image
			$username = $request->user()->name;
			$myUser = User::where('name', $username)->first();
			//processes and stores
			$file = $request->file('profileImage');			
			//$file = file_get_contents($request->profileVideo->path());
			//$file = $_POST['profileVideo'];			
			//getting timestamp
			$timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
			$name = $timestamp. '-' .$file->getClientOriginalName();
			$file->move(public_path().'/img/rpgGame/', $name);
			$picPath = url('/img/rpgGame/' . $name);
			$profileimage = new ProfileImage();
			$profileimage->user_id = $myUser->id;
			$profileimage->profile_image = $picPath;
			$myUser->profileImage()->delete();
			$myUser->profileImage()->save($profileimage);
			$myUser->save();
			$myUser['profile_image'] = $myUser->profileImage;
			$myUser['profile_video'] = $myUser->profileVideo;
			return response(['userData' => $myUser], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Profile image could not be processed. Please report to admin.'], 422);
		}
	}	
}