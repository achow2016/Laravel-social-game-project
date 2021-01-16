<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

	protected $table = 'posts';
	protected $primaryKey = 'post_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'postText', 'date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //'email_verified_at' => 'datetime',
    ];
	
	public function replies()
	{
		return $this->hasMany('App\Models\Reply', 'target_post_id', 'post_id');
	}
	
	public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
