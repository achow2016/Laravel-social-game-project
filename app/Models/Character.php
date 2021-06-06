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
		//map check on generation
		'onNewMap', 'mapComplete',
		//battle state
		'battle',
		'enemyId',
		'gameTurns', 'currentTurn', 'turnPosition', 'turnAction',
		'gameLevel', 'mapId', 'mapPosition',
        'ownerUser', 'charactername', 'race', 'class', 'avatar',
		'accuracy', 'agility', 'attack',
		'currentAccuracy', 'currentAgility', 'currentAttack',
		'damageDealt', 'damageReceived', 'itemsUsed', 'money', 'totalEarnings', 'score',
		'currentHealth', 'currentStamina',
		'health', 'stamina',
		'staminaRegen', 'healthRegen',
		'currentStaminaRegen', 'currentHealthRegen',
		'attackMultiplier', 'defenseMultiplier',
		'defense', 'currentDefense',
		'weapon','offHandEquipment','bodyEquipment','headEquipment','armsEquipment','legsEquipment',
		'stance', 'baseAttackCost',
		'combatRange',
		'effects'
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
		'stance' => 'array',
		'effects' => 'array'
    ];
	
	public function user() {
		return $this->belongsTo('App\Models\User', 'rpg_game_user_id', 'ownerUser');	
	}	
	
	public function race() {
		return $this->hasOne('App\Models\CharacterRace', 'race', 'race');
	}
	
	public function gameClass() {
		return $this->hasOne('App\Models\Class', 'class', 'class');
	}
	
	public function currentEnemy() {
		return $this->hasOne('App\Models\GameActiveEnemy', 'id', 'enemyId');
	}
	
	public function items() {
		return $this->hasMany('App\Models\ActiveGameCharacterItem', 'ownerId', 'id');
	}
	
	public function skills() {
		return $this->hasMany('App\Models\ActiveGameCharacterSkill', 'ownerId', 'id');
	}
	
	public function weapon() {
		return $this->hasOne('App\Models\GameWeapon', 'name', 'weapon');
	}
	
	public function legEquipment() {
		return $this->hasMany('App\Models\GameLegEquipment', 'name', 'legEquipment');
	}
	
	public function armsEquipment() {
		return $this->hasMany('App\Models\GameArmsEquipment', 'name', 'armsEquipment');
	}
	
	public function headEquipment() {
		return $this->hasMany('App\Models\GameHeadEquipment', 'name', 'headEquipment');
	}
	
	public function bodyEquipment() {
		return $this->hasMany('App\Models\GameBodyEquipment', 'name', 'bodyEquipment');
	}
	
	public function offHand() {
		return $this->hasMany('App\Models\GameOffhand', 'name', 'offHandEquipment');
	}
	
	//many other models and db game data tables needed to be added and seeded
	/*
	public function skills() {
		return $this->hasMany('App\Models\Skill', 'user_id', 'id');
	}
	
	public function statusEffects() {
		return $this->hasMany('App\Models\StatusEffect', 'user_id', 'id');
	}
	*/
}