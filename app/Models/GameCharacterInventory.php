<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class GameCharacterInventory extends Model
{
	//use HasFactory;
	
	protected $table = 'game_character_inventories';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'characterId'
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
	
	public function items() {
		return $this->hasMany('App\Models\ActiveGameItem', 'id', 'characterId');
	}
	
	public function character() {
		return $this->belongTo('App\Models\Character', 'character_id', 'characterId');
	}
}