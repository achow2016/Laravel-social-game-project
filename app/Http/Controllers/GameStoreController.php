<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
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
	
	public function cartAddItem(Request $request) 
	{
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