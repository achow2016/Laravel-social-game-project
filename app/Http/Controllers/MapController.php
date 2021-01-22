<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
use App\Models\GameMap;
use App\Models\GameMapTileset;
use App\Models\Character;

use DateTime;

class MapController extends Controller {
	
	public function generateMap(Request $request) 
	{
		$charId = Character::where('ownerUser', $request->user()->id)->first()->id;
		
		$gameMap = new GameMap();
		$gameMap->setAttribute('character_id', $charId);
		//generates random from 8 x 8
		$gameMap->setAttribute('startPoint', rand(0,8));
		$gameMap->setAttribute('endPoint', rand(0,8));
		$gameMap->save();
		
		//generates tileset here
		$tileSet = new GameMapTileset();
		
		
		//$gameMap->setAttribute('tileSet', $);
		
		
		
		return response(['status' => 'Generated map.'], 200);
	}	
}
?>	

