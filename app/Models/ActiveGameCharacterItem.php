<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class ActiveGameCharacterItem extends Model
{
	//use HasFactory;
	
	protected $table = 'game_active_character_items';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'itemId',
        'inventoryId',
        'name',
		'effect',
		'effectStackAmount',
		'effectStackLimit',
		'effectPercent',
		'effectDuration',
		'shopValue'
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
	
	public function originalItem() {
		return $this->hasOne('App\Models\GameSkill', 'id', 'itemId');
	}
	
	public function ownerInventory() {
		return $this->belongTo('App\Models\GameCharacterInventory', 'id', 'inventoryId');
	}
}