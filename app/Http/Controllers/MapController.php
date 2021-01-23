<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Log;

//models
use App\Models\GameMap;
use App\Models\GameMapTileset;
use App\Models\Character;

//use DateTime;

class MapController extends Controller {
	
	public function generateMap(Request $request) 
	{
		try {
			$charId = Character::where('ownerUser', $request->user()->id)->first()->id;
			
			$gameMap = new GameMap();
			$gameMap->setAttribute('character_id', $charId);
			
			//generates random from 8 x 8
			$gameMap->setAttribute('startPoint', [rand(0,8), rand(0,8)]);
			$gameMap->setAttribute('endPoint', [rand(0,8), rand(0,8)]);
			$gameMap->save();
			
			//generates tileset here, percentages of terrain
			$tileSet = new GameMapTileset();
			$tileSet->setAttribute('map_id', $gameMap->id);
			$allocLimit = 1;
			
			//grass
			$grassAlloc =  rand(1,10) / 10;
			$allocLimit -= $grassAlloc;
			
			//water remainder
			if($allocLimit > 0)
				$waterAlloc =  $allocLimit;
			else
				$waterAlloc = 0;
			
			//trees are placed over water or grass
			$treeCover = rand(1, 10) / 10;
			
			$tileSet->setAttribute('grassCover', $grassAlloc);
			$tileSet->setAttribute('waterCover', $waterAlloc);
			$tileSet->setAttribute('treeCover', $treeCover);	
			
			//creates the map in 2d array
			$map = [[]];
			for ($row = 0; $row < 8; $row++) {
				for ($col = 0; $col < 8; $col++) {
					$choice = rand(1, 10) / 10;
					if($choice <= $grassAlloc)
						$map[$row][$col] = (object) ['terrain' => 'grass', 'enemy' => '', 'item' => '', 'treeCover' => ''];
					else
						$map[$row][$col] = (object) ['terrain' => 'water', 'enemy' => '', 'item' => '', 'treeCover' => ''];
					
					if($choice <= $treeCover)
						$map[$row][$col]->treeCover = true;
					else
						$map[$row][$col]->treeCover = false;
				}
			}
			
			$tileSet->setAttribute('mapData', json_encode($map));
			$gameMap->tileset()->save($tileSet);		
			
			return response(['gameMap' => $gameMap, 'tileset' => $tileSet, 'mapData' => $map], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Map could not be listed. Please report to admin.'], 422);
		}	
	}	
}
?>	

