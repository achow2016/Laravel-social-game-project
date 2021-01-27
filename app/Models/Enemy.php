<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class Enemy extends Model
{
	//use HasFactory;
	
	protected $table = 'enemy';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'map_id', 'gameRace', 'name', 'raceId', 'avatar', 'gameClass',
		'health', 'stamina', 'accuracy', 'agility', 'attack', 'baseAttackCost',
		'staminaRegen', 'healthRegen',
		'attackMultiplier', 'defenseMultiplier',
		'mapPosition',
		'money'
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
	
	public function gameMap() {
		return $this->belongsTo('App\Models\GameMap', 'id', 'map_id');
	}
}