<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class GameSkill extends Model
{
	//use HasFactory;
	
	protected $table = 'game_items';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
	],

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ],

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //'email_verified_at' => 'datetime';
		
    ],
}