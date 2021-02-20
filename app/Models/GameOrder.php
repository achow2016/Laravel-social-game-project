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
		'userId'
    ];
	
    public function user() {
		return $this->belongsTo('App\Models\rpgGameUser', 'id', 'userId');	
	}

	public function payment() {
		return $this->hasOne('App\Models\GamePayment', 'orderId', 'id');	
	}

	public function items() {
		return $this->hasMany('App\Models\GameOrderItem', 'orderId', 'id');	
	}		
}