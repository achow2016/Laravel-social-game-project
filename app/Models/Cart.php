<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

	protected $table = 'carts';
	protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'date'
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
	
	public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
	
	public function cartItems()
	{
		return $this->hasMany('App\Models\CartItem', 'id', 'cart_id');
	}
}
