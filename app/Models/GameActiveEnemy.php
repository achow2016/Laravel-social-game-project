<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class GameActiveEnemy extends Model
{
	//use HasFactory;
	
	protected $table = 'game_active_enemies';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mapId', 'gameRace', 'name', 'raceId', 'avatar', 'gameClass',
		'health', 'stamina', 'accuracy', 'agility', 'attack', 'staminaRegen', 'healthRegen',
		'currentHealth', 'currentStamina', 'currentAccuracy', 'currentAgility',
		'currentAttack', 'currentStaminaRegen', 'currentHealthRegen',
		'baseAttackCost',
		'currentHealth', 'currentStamina',
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
		'mapPosition' => 'array',
		
    ];
	
	public function gameMap() {
		return $this->belongsTo('App\Models\GameMap', 'id', 'mapId');
	}
}