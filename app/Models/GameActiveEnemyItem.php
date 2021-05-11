<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Database\Factories\CharacterFactory;

class GameActiveEnemyItem extends Model
{
	//use HasFactory;
	
	protected $table = 'game_active_enemy_items';
	protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'itemId',
        'ownerId',
        'quantity'
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
		return $this->hasOne('App\Models\GameItem', 'id', 'itemId');
	}
	
	public function owner() {
		return $this->belongTo('App\Models\GameActiveEnemy', 'id', 'ownerId');
	}
}