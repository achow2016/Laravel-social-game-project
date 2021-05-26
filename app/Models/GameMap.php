<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\MapFactory;

class GameMap extends Model
{
	use HasFactory;
	
	protected $table = 'game_maps';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'level', 
		'startPoint'
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
		'startPoint' => 'array',
		'playerPosition' => 'array',
    ];
	
	public function character() {
		return $this->hasOne('App\Models\Character', 'mapId', 'id');	
	}	
	
	public function enemies() {
		return $this->hasMany('App\Models\GameActiveEnemy', 'mapId', 'id');
	}

	public function mapItems() {
		return $this->hasMany('App\Models\MapItem', 'mapId', 'id');
	}
	
	public function tileset() {
		return $this->hasOne('App\Models\GameMapTileset', 'mapId', 'id');
	}
}