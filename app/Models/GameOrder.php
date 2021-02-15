<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GameOrder extends Model {
	
	protected $table = 'orders';
	protected $primaryKey = 'id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'order_id', 'user_id'
    ];
	
    public function user() {
		return $this->belongsTo('App\Models\rpgGameUser', 'id', 'user_id');	
	}

	public function payment() {
		return $this->hasOne('App\Models\GamePayment', 'id', 'user_id');	
	}

	public function items() {
		return $this->hasMany('App\Models\GameOrderItem', 'order_id', 'order_id');	
	}		
}