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
		'money',
		'defense', 'currentDefense','stance',
		'weaponId','offHandEquipmentId','bodyEquipmentId','headEquipmentId','armsEquipmentId','legsEquipmentId',
		'combatRange', 'turnPosition', 'turnAction',
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
	
	public function gameMap() {
		return $this->belongsTo('App\Models\GameMap', 'id', 'mapId');
	}
	
	public function items() {
		return $this->hasMany('App\Models\GameActiveEnemyItem', 'ownerId', 'id');
	}
	
	public function skills() {
		return $this->hasMany('App\Models\GameActiveEnemySkill', 'ownerId', 'id');
	}
	
	public function weapon() {
		return $this->hasOne('App\Models\GameWeapon', 'id', 'weaponId');
	}
	
	public function legEquipment() {
		return $this->hasMany('App\Models\GameLegEquipment', 'id', 'legEquipmentId');
	}
	
	public function armsEquipment() {
		return $this->hasMany('App\Models\GameArmsEquipment', 'id', 'armsEquipmentId');
	}
	
	public function headEquipment() {
		return $this->hasMany('App\Models\GameHeadEquipment', 'id', 'headEquipmentId');
	}
	
	public function bodyEquipment() {
		return $this->hasMany('App\Models\GameBodyEquipment', 'id', 'bodyEquipmentId');
	}
	
	public function offHand() {
		return $this->hasMany('App\Models\GameOffhand', 'id', 'offHandEquipmentId');
	}
}