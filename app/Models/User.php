<?php

namespace App\Models;

//later used in laravel foritify
//use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\UserFactory;
use Laravel\Sanctum\HasApiTokens;
//use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
//class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
	use HasApiTokens;
	//Notifiable;
	use HasFactory;
	
	protected $table = 'rpggameusers';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password', 'avatar', 'profile_video', 'credits', 'membership', 'membership_start_date', 
			'membership_end_date', 'playtime', 'saveGame'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //'email_verified_at' => 'datetime',
    ];
	
	/*
	* Hashes passwords
	*/
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
	
	public function friends()
	{
		return $this->hasMany('App\Models\rpgGameFriend', 'rpg_game_user_id', 'id');
	}
	
	public function messages()
	{
		return $this->hasMany('App\Models\rpgGameMessage', 'rpg_game_user_id', 'id');
	}

	public function profileImage()
	{
		return $this->hasOne('App\Models\ProfileImage', 'user_id', 'id');
	}
	
	public function profileVideo()
	{
		return $this->hasOne('App\Models\ProfileVideo', 'user_id', 'id');
	}
	
	public function payments()
	{
		return $this->hasMany('App\Models\GamePayment', 'user_id', 'id');
	}
	
	public function character()
	{
		return $this->hasOne('App\Models\Character', 'ownerUser', 'id');
	}
	
	public function score() {
		return $this->hasOne('App\Models\GameScoreRecord', 'userId', 'id');
	}
	
	public function posts()
	{
		return $this->hasMany('App\Models\Post', 'user_id', 'id');
	}
	
	public function replies()
	{
		return $this->hasMany('App\Models\Reply', 'user_id', 'id');
	}
	
	public function cart()
	{
		return $this->hasOne('App\Models\Cart', 'user_id', 'id');
	}
	
	public function screenshots()
	{
		return $this->hasMany('App\Models\GameScreenshot', 'user_id', 'id');
	}
}
