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
        'ownerUser', 'charactername', 'raceId', 'avatar',
		'page', 'chapter', 
		'health', 'stamina', 'accuracy', 'agility', 'attack',
		'mapPosition',
		'scoreTotal', 'damageDone', 'damageReceived', 'chaptersCleared', 'money', 'earningsTotal',
		'staminaRegen', 'healthRegen',
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
    ];
	
	public function user() {
		return $this->belongsTo('App\Models\User', 'rpg_game_user_id', 'ownerUser');	
	}	
	
	public function race() {
		return $this->hasOne('App\Models\CharacterRace', 'id', 'raceId');
	}
	
	public function gameMap() {
		return $this->hasOne('App\Models\GameMap', 'map_id', 'id');
	}
	
	//many other models and db game data tables needed to be added and seeded
	/*
	public function class() {
		return $this->hasOne('App\Models\Class', 'user_id', 'id');
	}
	
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