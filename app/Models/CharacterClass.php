<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CharacterRaceFactory;

class CharacterClass extends Model
{
	use HasFactory;
	
	protected $table = 'character_classes';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class', 'weaknesses', 'resistances', 'skills', 'stamina', 'accuracy', 'attack', 'baseAttackCost', 'staminaRegen', 'healthRegen', 'agility', 'defense'
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
		'weaknesses' => 'array',
		'resistances' => 'array',		
		'skills' => 'array'		
    ];
	
	public function character() {
		return $this->belongsTo('App\Models\Character', 'class', 'class');	
	}	
}
