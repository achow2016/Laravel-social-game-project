<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\GameScreenshotFactory;

class ProfileImage extends Model
{
	//use HasFactory;
	
	protected $table = 'user_profile_images';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'profile_image'
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
    ];
	
	public function user() {
		return $this->belongsTo('App\Models\User', 'id', 'user_id');	
	}	
	
	public function enemies() {
		return $this->hasMany('App\Models\Enemy', 'map_id', 'id');
	}
	
	public function mapItems() {
		return $this->hasMany('App\Models\MapItem', 'id', 'character_id');
	}
	
	public function tileset() {
		return $this->hasOne('App\Models\GameMapTileset', 'map_id', 'character_id');
	}
}