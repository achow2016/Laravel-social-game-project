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
			$charObj = Character::where('ownerUser', $request->user()->id)->first();
			$charId = $charObj->id;
			$charMapId = $charObj->mapId;
			
			//creates and matchs the game map to the character and enemies
			$existingMap = GameMap::where('id', $charMapId)->first();
			if($existingMap) {
				$existingMap->startPoint = [rand(0,8), rand(0,8)];
				$existingMap->playerPosition = $existingMap->startPoint;
				
				//attach enemy and player after start position defined
				//attach player
				$existingMap->save();
				
				
				$tileCheck = $existingMap->tileset()->first();
				
				if($tileCheck) 
					$existingMap->tileset()->delete();
				
				//generates tileset here, percentages of terrain
				$tileSet = new GameMapTileset();
				$tileSet->setAttribute('mapId', $existingMap->id);
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
				
				//creates the map tileset in 2d array
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
				//sets map tile data to tileset
				$tileSet->setAttribute('mapData', json_encode($map));	
				//saves tileset onto map
				$existingMap->tileset()->save($tileSet);
				//places character onto map starting position
				$charObj->mapPosition = $existingMap->startPoint;
				
				return response(['gameMap' => $existingMap, 'tileset' => $tileSet, 'mapData' => $map], 200);
			}
			else {
				$gameMap = new GameMap();				
				$gameMap->setAttribute('startPoint', [rand(0,8), rand(0,8)]);
				$gameMap->setAttribute('level', 1);
				
				//attach enemy and player after start position defined
				$gameMap->save();
				$charObj->mapId = $gameMap->id;
				$charObj->mapPosition = $gameMap->startPoint;
				$charObj->save();
				
				//replace or generate tileset data for map
				$tileCheck = $gameMap->tileset()->first();
				if($tileCheck)
					$gameMap->tileset()->delete();
				
				//generates tileset here, percentages of terrain
				$tileSet = new GameMapTileset();
				$tileSet->setAttribute('mapId', $gameMap->id);
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
				
				//creates the map tileset in 2d array
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
				
				//sets tileset onto map
				$tileSet->setAttribute('mapData', json_encode($map));
				$gameMap->tileset()->save($tileSet);
			
				return response(['gameMap' => $gameMap, 'tileset' => $tileSet, 'mapData' => $map], 200);
			}		
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Map could not be listed. Please report to admin.'], 422);
		}	
	}	
	
	//gets generated map in game
	public function getMap(Request $request) 
	{
		try {
			$charObj = Character::where('ownerUser', $request->user()->id)->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			
			if($existingMap)
				return response(['playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData], 200);
			else
				return response(['status' => 'No map, please start a new game.'], 422);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Map could not be listed. Please report to admin.'], 422);
		}	
	}	
	
	//moves character position
	public function moveCharacter(Request $request) 
	{
		try {
			$charObj = Character::where('ownerUser', $request->user()->id)->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			$movementChoice = null;
			
			Log::debug($request);
			
			if($existingMap) {
				switch($request->direction) {
					case 'up':
						$movementChoice = 'up';
						break;
					default:	
						break;
				}		
			
				return response(['move' => $movementChoice, 'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData], 200);
			}
			else
				return response(['status' => 'No map, please start a new game.'], 422);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Could not update player position. Please report to admin.'], 422);
		}
	}
}
?>	

