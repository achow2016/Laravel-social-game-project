<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\MapFactory;

class GameMap extends Model
{
	use HasFactory;
	
	protected $table = 'game_map';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'startPoint', 'endPoint'
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
		'endPoint' => 'array'
    ];
	
	public function character() {
		return $this->belongsTo('App\Models\Character', 'id', 'character_id');	
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