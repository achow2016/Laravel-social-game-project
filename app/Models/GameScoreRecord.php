<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class GameScoreRecord extends Model
{
	//use HasFactory;
	
	protected $table = 'game_score_records';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'gameLevel', 'ownerUser', 'charactername', 'race', 'class', 'avatar',
		'accuracy', 'agility', 'attack',
		'damageDealt', 'damageReceived', 'itemsUsed', 'money', 'totalEarnings', 'score',
		'health', 'stamina',
		'staminaRegen', 'healthRegen',
		'defense',
		'weapon','offHandEquipment','bodyEquipment','headEquipment','armsEquipment','legsEquipment',
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
    ];
	
	public function user() {
		return $this->belongsTo('App\Models\User', 'rpg_game_user_id', 'ownerUser');	
	}	
}