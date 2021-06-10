<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
use App\Models\User;
use App\Models\GameMap;
use App\Models\GameMapTileset;
use App\Models\Character;
use App\Models\GameActiveEnemy;
use App\Models\ActiveGameCharacterItem;
use App\Models\GameItem;

//special 
use App\Traits\GameTurnLogic;
use App\Traits\EnemyLogic;
//use DateTime;

//Log::debug($mapGrid); player placed on matching 2d array


class MapController extends Controller {

	use GameTurnLogic;
	use EnemyLogic;

	public function generateMap(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$charObj = Character::where('ownerUser', $request->user()->id)->first();
			//$charObj = $user->character()->first();
			
			if(!$charObj) {
				return response(['message' => 'No character found, please create a new character.'], 422);
			}
			
			//if already placed on new map denies generation of another one
			if($charObj->mapComplete == false && $charObj->mapId != null) {
				$lastMap = GameMap::where('id', $charObj->mapId)->first();
				$lastMapTiles = $lastMap->tileset()->first();
				$mapData = json_decode($lastMapTiles->mapData);
			
				return response([
				'gameLevel' => $charObj->gameLevel,
				'gameMap' => $lastMap,
				'tileset' => $lastMapTiles,
				'mapData' => $mapData,
				'message' => 'New map not generated, game in progress.'
				], 200);
			}
			
			$oldMapId = $charObj->mapId;
		
			$gameMap = new GameMap();				
			$gameMap->setAttribute('startPoint', [rand(0,7), rand(0,7)]);
			$gameMap->setAttribute('level', 1);
			
			//attach enemy and player after start position defined, mark as having been placed on new map already
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
			
			//deletes old tileset and map
			if($oldMapId != null) {
				$existingMap = GameMap::where('id', $oldMapId)->first();
				$tileCheck = $existingMap->tileset()->first();
				if($tileCheck) 
					$existingMap->tileset()->delete();
				$existingMap->delete();
			}
			return response([
				'gameLevel' => $charObj->gameLevel,
				'gameMap' => $gameMap,
				'tileset' => $tileSet,
				'mapData' => $map
			], 200);		
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
	//array is row, col with top left being [0,0]
	public function moveCharacter(Request $request) 
	{	
		try {
			$charObj = Character::where('ownerUser', $request->user()->id)->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			//$enemyCoords = $existingMap->enemies()->get()->pluck('mapPosition');
			//$enemies = $existingMap->enemies()->get();
			$enemies = GameActiveEnemy::where('mapId', $existingMap->id)->get();
			$playerCoords = $charObj->mapPosition;
			
			$movementChoice = null;
			$currentRow = $charObj->mapPosition[0];
			$currentColumn = $charObj->mapPosition[1];
			
			if($existingMap) {				
				$responseArray[] = array();
				switch($request->direction) {
					//NS or EW
					case 'up':
						if($currentRow == 0) {
							$responseArray[] = ([
								'message' => 'Path blocked', 'move' => $movementChoice, 
								'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
							]);
						}	
						$movementChoice = 'north';
						if($currentRow >= 1) {
							$currentRow--;
							foreach($enemies as $enemy) {
								if([$currentRow, $currentColumn] === $enemy->mapPostion) {
									$responseArray['message'] = 'Path blocked by enemy';
									$responseArray['move'] = $charObj->mapPosition;
									$responseArray['playerPosition'] = 'Path blocked by enemy';
									$responseArray['mapData'] = $existingMap->tileset()->first()->mapData;
								}	
							}
							$charObj->setAttribute('mapPosition', [$currentRow, $currentColumn]);
						}	
						break;
					case 'down':
						if($currentRow == 7) {
							$responseArray[] = ([
								'message' => 'Path blocked', 'move' => $movementChoice, 
								'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
							]);
						}
						$movementChoice = 'south';
						if($currentRow <= 6) {
							$currentRow++;
							foreach($enemies as $enemy) {
								if([$currentRow, $currentColumn] === $enemy->mapPostion) {
									$responseArray[] = ([
										'message' => 'Path blocked by enemy', 'move' => $movementChoice, 
										'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
									]);
								}	
							}
							$charObj->setAttribute('mapPosition', [$currentRow, $currentColumn]);
						}	
						break;
					case 'left':
						if($currentColumn == 0) {
							$responseArray[] = ([
								'message' => 'Path blocked', 'move' => $movementChoice, 
								'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
							]);
						}
						$movementChoice = 'west';
						if($currentColumn >= 1) {
							$currentColumn--;
							foreach($enemies as $enemy) {
								if([$currentRow, $currentColumn] === $enemy->mapPostion) {
									$responseArray[] = ([
										'message' => 'Path blocked by enemy', 'move' => $movementChoice, 
										'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
									]);
								}	
							}
							$charObj->setAttribute('mapPosition', [$currentRow, $currentColumn]);
						}	
						break;
					case 'right':
						if($currentColumn == 7) {
							$responseArray[] = ([
								'message' => 'Path blocked', 'move' => $movementChoice, 
								'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
							]);
						}
						$movementChoice = 'east';
						if($currentColumn <= 6) {
							$currentColumn++;
							foreach($enemies as $enemy) {
								if([$currentRow, $currentColumn] === $enemy->mapPostion) {
									$responseArray[] = ([
										'message' => 'Path blocked by enemy', 'move' => $movementChoice, 
										'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
									]);
								}	
							}
							$charObj->setAttribute('mapPosition', [$currentRow, $currentColumn]);
						}	
						break;
					//diagonals
					case 'upLeft':
						$movementChoice = 'northwest';
						if($currentRow >= 1 && $currentColumn >= 1) {
							$currentRow--;
							$currentColumn--;
							foreach($enemies as $enemy) {
								if([$currentRow, $currentColumn] === $enemy->mapPostion) {
									$responseArray[] = ([
										'message' => 'Path blocked by enemy', 'move' => $movementChoice, 
										'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
									]);
								}	
							}
							$charObj->setAttribute('mapPosition', [$currentRow, $currentColumn]);
						}	
						break;
					case 'upRight':
						$movementChoice = 'northeast';
						if($currentRow >= 1 && $currentColumn <= 6) {
							$currentRow--;
							$currentColumn++;
							foreach($enemies as $enemy) {
								if([$currentRow, $currentColumn] === $enemy->mapPostion) {
									$responseArray[] = ([
										'message' => 'Path blocked by enemy', 'move' => $movementChoice, 
										'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
									]);
								}	
							}
							$charObj->setAttribute('mapPosition', [$currentRow, $currentColumn]);
						}	
						break;
					case 'downLeft':
						$movementChoice = 'southwest';
						if($currentRow <= 6 && $currentColumn >= 1) {
							$currentRow++;
							$currentColumn--;
							foreach($enemies as $enemy) {
								if([$currentRow, $currentColumn] === $enemy->mapPostion) {
									$responseArray[] = ([
										'message' => 'Path blocked by enemy', 'move' => $movementChoice, 
										'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
									]);
								}	
							}
							$charObj->setAttribute('mapPosition', [$currentRow, $currentColumn]);
						}	
						break;
					case 'downRight':
						$movementChoice = 'southeast';
						if($currentRow <= 6 && $currentColumn <= 6) {
							$currentRow++;
							$currentColumn++;
							foreach($enemies as $enemy) {
								if([$currentRow, $currentColumn] === $enemy->mapPostion) {
									$responseArray[] = ([
										'message' => 'Path blocked by enemy', 'move' => $movementChoice, 
										'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
									]);
								}	
							}
							$charObj->setAttribute('mapPosition', [$currentRow, $currentColumn]);
						}	
						break;	
					case 'wait':
						$movementChoice = 'wait';
						//	$responseArray[] = ([
						//		'message' => 'Player is waiting.', 'move' => $movementChoice, 
						//		'playerPosition' => $charObj->mapPosition, 'mapData' => $existingMap->tileset()->first()->mapData
						//	]);						
						break;	
					default:	
						break;
				}
				
				if($movementChoice == 'wait')	
					$responseArray['message'] = 'Player waits.';
				else
					$responseArray['message'] = 'Moved ' . $movementChoice . '.';
				
				$responseArray['move'] = $movementChoice;
				$responseArray['playerPosition'] = $charObj->mapPosition;
				$responseArray['mapData'] = $existingMap->tileset()->first()->mapData;
				
				//increments turn number, movement count, effect updates, and saves
				
				$updatedEffects = $this->updateEffects($request);
				if($updatedEffects != null)
					foreach($updatedEffects as $update) {
						$responseArray['message'] = $responseArray['message'] . $update;
					}
				
				$charObj->currentTurn = $charObj->currentTurn + 1;
				if($charObj->currentTurn > $charObj->gameTurns)
					$charObj->currentTurn = 1;
				
				if(!$charObj->mapComplete) {
					$charObj->squaresMoved = $charObj->squaresMoved + 1;	
					$charObj->score = $charObj->score + 1;
				}
				
				$charObj->save();
				return response($responseArray, 200);
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