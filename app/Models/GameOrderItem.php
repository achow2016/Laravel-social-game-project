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
		"order_id", "item_id", "name", "itemDescription", "type", "duration", "quantity", "cost"
    ];
	
    public function order() {
		return $this->belongsTo('App\Models\GameOrder', 'order_id', 'order_id');	
	}
	
	public function itemDetail() {
		return $this->belongsTo('App\Models\CashShopItem', 'id', 'item_id');	
	}
}