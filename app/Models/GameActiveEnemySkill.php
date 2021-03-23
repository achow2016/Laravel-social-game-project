<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class ActiveGameEnemySkill extends Model
{
	//use HasFactory;
	
	protected $table = 'game_active_enemy_skills';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ownerId',
		'skillId',
		'name',
		'stance',
		'bodyTarget',
		'debuff',
		'debuffEffectPercentage',
		'debuffDuration',
		'buff',
		'buffEffectPercentage',
		'buffDuration',
		'range',
		'staminaCost',
		'effectChance',
		'damagePenalty'
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
	
	public function owner() {
		return $this->belongsTo('App\Models\GameActiveEnemy', 'id', 'ownerId');
	}
	
	public function originalSkill() {
		return $this->belongTo('App\Models\GameSkill', 'id', 'skillId');
	}
}