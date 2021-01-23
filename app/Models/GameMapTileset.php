<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameMapTileset extends Model
{
	use HasFactory;
	
	protected $table = 'tileset';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'map_id', 'mapData', 'grassCover', 'waterCover', 'treeCover'
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
		'mapData' => 'array'
		
    ];
	
	public function map() {
		return $this->belongsTo('App\Models\GameMap', 'id', 'map_id');	
	}	
}