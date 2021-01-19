<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{

	protected $table = 'cart_items';
	protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id', 'name', 'quantity', 'price'
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
	
	public function cart()
    {
        return $this->belongsTo('App\Models\Cart', 'cart_id', 'user_id');
    }
}
