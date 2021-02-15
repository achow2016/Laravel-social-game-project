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
		try {
			$cashShopItems = CashShopItem::all();
			return response(['cashItems' => $cashShopItems, 'status' => 'Store items listed.'], 200);
		} 
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Store items could not be listed. Please report to admin.'], 422);
		}	
	}
	
	public function addCartItem(Request $request) 
	{
		try {
			//gets user, cart item detail from db
			$name = $request->user()->name;
			$user = User::where('name', $name)->first();
			$cashShopItem = CashShopItem::where('id', $request->itemId)->first();
			//creates user cart if none present
			if(!$user->cart()->exists()) {
				$this->createCart($name);
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
			return response(['status' =>'Cart item created.'], 200);
		} 
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Cart item could not be created. Please report to admin.'], 422);
		}
	}
	
	//create cart for user
	public function createCart($username)
	{
		try {
			$user = User::where('name', $username)->first();
			$cart = new Cart();
			$cart->setAttribute('user_id', $user->id);
			$date = new DateTime("now");
			$cart->setAttribute('date', $date);
			$user->cart()->save($cart);
			return response(['status' => 'Cart created for user.'], 200);
		} 
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Cart ould not be created. Please report to admin.'], 422);
		}
	}
	
	
	//gets cart total and sum of all items in cart
	public function getCartItems(Request $request)
	{
		try {
			$name = $request->user()->name;
			$user = User::where('name', $name)->first();
			$cart = Cart::where('user_id', $user->id)->first();
			if($cart)
				$cartItems = $cart->cartItems()->get();
			else {
				$this->createCart($name);
				$cartItems = null;
				$cartTotal = 0;
			}
			if($cartItems != null)
				$cartTotal = $cart->cartItems()->sum(\DB::raw('cart_items.price * cart_items.quantity'));
			
			return response(['cartTotal' => $cartTotal, 'cartItems' => $cartItems], 200);
		} 
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Cart items could not be listed. Please report to admin.'], 422);
		}
	}	
	
	public function deleteCartItem(Request $request) 
	{
		try {
			$name = $request->user()->name;
			$user = User::where('name', $name)->first();
			$cart = Cart::where('user_id', $user->id)->first();
			$cartItems = $cart->cartItems();
			$cartItems->where('id', $request->itemId)->delete();
			$cart->save();
			return response(['status' => 'Cart item deleted.'], 200);
		} 
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Cart item could not be deleted. Please report to admin.'], 422);
		}		
	}
	
	public function updateCartItem(Request $request) 
	{
		try {
			$name = $request->user()->name;
			$user = User::where('name', $name)->first();
			$cart = Cart::where('user_id', $user->id)->first();
			$cartItems = $cart->cartItems();
			$cartItems->where('id', $request->itemId)->update(array('quantity' => $request->quantity));
			$cart->save();
			return response(['status' => 'Cart item updated.'], 200);
		} 
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Cart item could not be updated. Please report to admin.'], 422);
		}
	}
	
	public function checkout(Request $request) 
	{
	}
	
}
?>