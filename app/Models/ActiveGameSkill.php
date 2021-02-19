<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class ActiveGameSkill extends Model
{
	//use HasFactory;
	
	protected $table = 'game_active_character_skills';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'characterId',
		'skillId',
		'name',
		'bodyTarget',
		'stanceResult',
		'debuff',
		'debuffPercent',
		'debuffDuration',
		'debuffStackQuantity',
		'debuffStackMax',
		'effect',
		'effectQuantity',
		'range',
		'accuracyPercent',
		'meleePenaltyPercent',
		'staminaCost'
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
	
	public function skill() {
		return $this->hasOne('App\Models\GameSkill', 'id', 'skillId');
	}
	
	public function Skillset() {
		return $this->belongTo('App\Models\GameSkillset', 'characterId', 'characterId');
	}
}