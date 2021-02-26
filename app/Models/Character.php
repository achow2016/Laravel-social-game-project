<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CharacterFactory;

class Character extends Model
{
	use HasFactory;
	
	protected $table = 'character';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'gameLevel', 'mapId', 'mapPosition',
        'ownerUser', 'charactername', 'raceId', 'classId', 'avatar',
		'page', 'chapter', 
		'accuracy', 'agility', 'attack',
		'currentAccuracy', 'currentAgility', 'currentAttack',
		'scoreTotal', 'damageDone', 'damageReceived', 'chaptersCleared', 'money', 'earningsTotal',
		'currentHealth', 'currentStamina',
		'health', 'stamina',
		'staminaRegen', 'healthRegen',
		'currentStaminaRegen', 'currentHealthRegen',
		'attackMultiplier', 'defenseMultiplier'
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
	
	public function user() {
		return $this->belongsTo('App\Models\User', 'rpg_game_user_id', 'ownerUser');	
	}	
	
	public function race() {
		return $this->hasOne('App\Models\CharacterRace', 'id', 'raceId');
	}
	
	public function gameMap() {
		return $this->belongsTo('App\Models\GameMap', 'id', 'mapId');
	}
	
	public function gameClass() {
		return $this->hasOne('App\Models\Class', 'id', 'classId');
	}
	
	//many other models and db game data tables needed to be added and seeded
	/*
	public function skills() {
		return $this->hasMany('App\Models\Skill', 'user_id', 'id');
	}
	
	public function weapons() {
		return $this->hasMany('App\Models\Weapon', 'user_id', 'id');
	}
	
	public function armors() {
		return $this->hasMany('App\Models\Armor', 'user_id', 'id');
	}
	
	public function items() {
		return $this->hasMany('App\Models\Item', 'user_id', 'id');
	}
	
	public function enemies() {
		return $this->hasMany('App\Models\Enemy', 'user_id', 'id');
	}
	
	public function statusEffects() {
		return $this->hasMany('App\Models\StatusEffect', 'user_id', 'id');
	}
	*/
}