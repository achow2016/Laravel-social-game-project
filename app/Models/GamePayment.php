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
		'amount', 'userId'
    ];
	
    public function user() {
		return $this->belongsTo('App\Models\rpgGameUser', 'id', 'userId');	
	}	
	
	public function order() {
		return $this->belongsTo('App\Models\GameOrder', 'id', 'userId');	
	}	
}