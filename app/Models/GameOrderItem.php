<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GameOrderItem extends Model {
	
	protected $table = 'ordered_items';
	protected $primaryKey = 'id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		"orderId", "itemId", "name", "itemDescription", "type", "duration", "quantity", "cost"
    ];
	
    public function order() {
		return $this->belongsTo('App\Models\GameOrder', 'orderId', 'orderId');	
	}
	
	public function itemDetail() {
		return $this->belongsTo('App\Models\CashShopItem', 'id', 'itemId');	
	}
}