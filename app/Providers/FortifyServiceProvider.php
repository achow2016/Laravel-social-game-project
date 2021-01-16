<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

use App\Models\User;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		/*
		Fortify::loginView(function () {
			return redirect('/loginForm');
			
		});
		*/
		
		/*
		Fortify::loginView(fn () => view('loginForm'));
		
		Fortify::authenticateUsing(function (Request $request) {
			$user = User::where('email', $request->email)->first();

			if ($user &&
				Hash::check($request->password, $user->password)) {
				return $user;
			}
		});
		
		Fortify::registerView(fn () => view('register'));
		*/
		/*
		Fortify::registerView(function () {
			return redirect('/register');
		});

		Fortify::requestPasswordResetLinkView(function () {
			return redirect('/resetPass');
		});

		Fortify::resetPasswordView(function ($request) {
			return redirect('/resetPass')->with(['request' => $request]);
		});

		
		Fortify::verifyEmailView(function () {
			return redirect('/verifyEmail');
		});
		*/
	
        //Fortify::createUsersUsing(CreateNewUser::class);
        //Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        //Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        //Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    }
}
