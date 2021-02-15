<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GamePayment extends Model{
	
	protected $table = 'payments';
	protected $primaryKey = 'id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'amount', 'user_id'
    ];
	
    public function user() {
		return $this->belongsTo('App\Models\rpgGameUser', 'id', 'user_id');	
	}	
	
	public function order() {
		return $this->belongsTo('App\Models\GameOrder', 'id', 'user_id');	
	}	
}