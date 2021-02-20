<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class GameEnemy extends Model
{
	//use HasFactory;
	
	protected $table = 'game_enemies';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gameRace', 'name', 'raceId', 'avatar', 'gameClass',
		'health', 'stamina', 'accuracy', 'agility', 'attack', 'baseAttackCost',
		'staminaRegen', 'healthRegen',
		'attackMultiplier', 'defenseMultiplier',
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
		return $this->belongsTo('App\Models\GameMap', 'id', 'mapId');
	}
	
	public function race() {
		return $this->hasOne('App\Models\CharacterRace', 'id', 'raceId');
	}
}