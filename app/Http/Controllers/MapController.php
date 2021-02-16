<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
			
			//generates tileset here, percentages of terrain
			$tileSet = new GameMapTileset();
			$tileSet->setAttribute('map_id', $charId);
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
			
			$existingMap = GameMap::where('character_id', $charId)->first();
			if($existingMap) {
				$existingMap->startPoint = [rand(0,8), rand(0,8)];
				$existingMap->endPoint = [rand(0,8), rand(0,8)];
				$existingMap->save();
				$tileCheck = $existingMap->tileset()->first();
				if($tileCheck)
					$existingMap->tileset()->delete();
				$existingMap->tileset()->save($tileSet);
				return response(['gameMap' => $existingMap, 'tileset' => $tileSet, 'mapData' => $map], 200);
			}
			else {
				$gameMap = new GameMap();
				$gameMap->setAttribute('character_id', $charId);				
				$gameMap->setAttribute('startPoint', [rand(0,8), rand(0,8)]);
				$gameMap->setAttribute('endPoint', [rand(0,8), rand(0,8)]);
				$gameMap->save();
				$tileCheck = $gameMap->tileset()->first();
				if($tileCheck)
					$gameMap->tileset()->delete();
				$gameMap->tileset()->save($tileSet);		
				return response(['gameMap' => $gameMap, 'tileset' => $tileSet, 'mapData' => $map], 200);
			}
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Map could not be listed. Please report to admin.'], 422);
		}	
	}	
	
	public function getmap(Request $request) 
	{
		try {
			$charId = Character::where('ownerUser', $request->user()->id)->first()->id;
			$existingMap = GameMap::where('character_id', $charId)->first();
			Log::debug($existingMap->tileset()->first());
			if($existingMap)
				return response(['mapData' => $existingMap->tileset()->first()->mapData], 200);
			else
				return response(['status' => 'No map, please start a new game.'], 422);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Map could not be listed. Please report to admin.'], 422);
		}	
	}	
}
?>	

