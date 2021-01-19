<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CashShopItem;

use DateTime;

class GameStoreController extends Controller {

	public function getStoreItems() 
	{
		$cashShopItems = CashShopItem::all();
		return response(['cashItems' => $cashShopItems], 200);
		
	}
	
	public function addCartItem(Request $request) 
	{
		//gets user, cart item detail from db
		$name = $request->user()->name;
		$user = User::where('name', $name)->first();
		$cashShopItem = CashShopItem::where('id', $request->itemId)->first();
		//creates user cart if none present
		$cart = null;
		if(!$user->cart()->exists()) {
			$cart = new Cart();
			$cart->setAttribute('user_id', $user->id);
			$date = new DateTime("now");
			$cart->setAttribute('date', $date);
			$user->cart()->save($cart);
		}
		//check if item already in db, if so increment amounts
		$doubledItem = CartItem::where('cart_id', $user->id)->where('name', $cashShopItem->name)->first();
		if($doubledItem) {
			$oldQuantity = 	$doubledItem->quantity;
			$itemCost = $cashShopItem->cost;
			$newQuantity = $oldQuantity + $request->quantity;
			$newPrice = $newQuantity * $itemCost;
			$doubledItem->quantity = $newQuantity;
			$doubledItem->price = $newPrice;
			$doubledItem->save();
			return response('Cart item updated.', 200);
		}			
		//adds new item to cart item list	
		$cartItem = new CartItem();
		$cartItem->setAttribute('cart_id', $user->id);
		$cartItem->setAttribute('name', $cashShopItem->name);
		$cartItem->setAttribute('quantity', $request->quantity);
		$cartItem->setAttribute('price', $cashShopItem->cost);
		$cart = Cart::where('user_id', $user->id)->first();
		$cart->cartItems()->save($cartItem);
		return response('Cart item created.', 200);
	}
	
	public function getCartItems(Request $request)
	{
		$name = $request->user()->name;
		$user = User::where('name', $name)->first();
		$cart = Cart::where('user_id', $user->id)->first();
		$cartItems = $cart->cartItems()->get();
		return response(['cartItems' => $cartItems], 200);
	}	
	
	public function cartRemoveItem(Request $request) 
	{
	}
	
	public function cartModifyItem(Request $request) 
	{
	}
	
	public function checkout(Request $request) 
	{
	}
	
}
?>