<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
use App\Models\Character;
use App\Models\CharacterRace;
use App\Models\CharacterClass;
use App\Models\GameEnemy;
use App\Models\GameItem;
use App\Models\GameActiveEnemy;
use App\Models\GameActiveEnemyItem;
use App\Models\User;
use App\Models\GameMap;
use App\Models\GameMapTileset;

use App\Models\GameOffhand;
use App\Models\GameWeapon;
use App\Models\GameArmsEquipment;
use App\Models\GameBodyEquipment;
use App\Models\GameHeadEquipment;
use App\Models\GameLegEquipment;

//use DateTime;

class EnemyController extends Controller {

	//generates enemies and return coordinates for display on map builder
	public function generateEnemies(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			
			$charObj = $user->character()->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			
			$gameLevel = $charObj->gameLevel;
			$enemyChoices = GameEnemy::where('gameLevel', $gameLevel)->get();
			$enemyChoicesCount = $enemyChoices->count();
			
			$usedMapPositions[] = $charObj->mapPosition;
			$freeSpaceFound = false;
			
			//Log::debug($mapGrid); player placed on matching 2d array
			
			//gets map tileset as array for placing enemy data
			$map = $existingMap->tileset()->first()->pluck('mapData');
			$mapDecoded = json_decode($map[0], TRUE);
			
			for($i = 0; $i < $gameLevel; $i++) {
				$enemyChoice = max((rand(0,$enemyChoicesCount - 1)), 0 );
				$strengthAlloc = rand(0, 12);
				$enduranceAlloc = max((rand(0, 12) - $strengthAlloc), 0);
				$lifeAlloc = max((rand(0, 12) - $strengthAlloc - $enduranceAlloc), 0);
				
				$enemyRace = CharacterRace::where('race', $enemyChoices[$enemyChoice]->gameRace)->first();
				$enemyClass = CharacterClass::where('name', $enemyChoices[$enemyChoice]->gameClass)->first();			
				
				$enemyOffhand = GameOffhand::where('name', $enemyChoices[$enemyChoice]->offHand)->first();
				$enemyWeapon = GameWeapon::where('name', $enemyChoices[$enemyChoice]->weapon)->first();
				$enemyArmsEquip = GameArmsEquipment::where('name', $enemyChoices[$enemyChoice]->armsEquipment)->first();
				$enemyBodyEquip = GameBodyEquipment::where('name', $enemyChoices[$enemyChoice]->bodyEquipment)->first();
				$enemyHeadEquip = GameHeadEquipment::where('name', $enemyChoices[$enemyChoice]->headEquipment)->first();
				$enemyLegEquip = GameLegEquipment::where('name', $enemyChoices[$enemyChoice]->legsEquipment)->first();
				
				$equipArray = [];
				array_push($equipArray, $enemyOffhand,$enemyWeapon,$enemyArmsEquip,$enemyBodyEquip,$enemyHeadEquip,$enemyLegEquip);
				
				$enemy = new GameActiveEnemy();
					
				while(!$freeSpaceFound) {
					$randEnemyPosition = [rand(0,7), rand(0,7)];
					$key = array_search($randEnemyPosition, $usedMapPositions);
					if(!$key) {
						$freeSpaceFound = true;
						$usedMapPositions[] = $randEnemyPosition;
						$enemy->setAttribute('mapPosition', $randEnemyPosition);
					}	
				}
				$freeSpaceFound = false;
				
				$additionalAttack = 0; 
				$additionalArmour = 0; 
				$additionalDefense = 0; 
				
				foreach($equipArray as $value) {
					$additionalAttack = $additionalAttack + $value->attack; 
					$additionalArmour = $additionalArmour + $value->armour; 
					$additionalDefense = $additionalDefense + $value->defense; 
				}	
					
				$enemy->setAttribute('raceId', $enemyRace->id);
				$enemy->setAttribute('classId', $enemyClass->id);
				$enemy->setAttribute('name', $enemyChoices[$enemyChoice]->name);
				$enemy->setAttribute('health', $enemyRace->health + $enemyClass->health + $lifeAlloc);
				$enemy->setAttribute('currentHealth', $enemyRace->health + $enemyClass->health + $lifeAlloc);
				$enemy->setAttribute('healthRegen', $enemyRace->healthRegen + $enemyClass->healthRegen);
				$enemy->setAttribute('currentHealthRegen', $enemyRace->healthRegen + $enemyClass->healthRegen);
				$enemy->setAttribute('stamina', $enemyRace->stamina + $enemyClass->stamina + $enduranceAlloc);
				$enemy->setAttribute('currentStamina', $enemyRace->stamina + $enemyClass->stamina + $enduranceAlloc);
				$enemy->setAttribute('staminaRegen', $enemyRace->staminaRegen + $enemyClass->staminaRegen);
				$enemy->setAttribute('currentStaminaRegen', $enemyRace->staminaRegen + $enemyClass->staminaRegen);
				$enemy->setAttribute('agility', $enemyRace->agility + $enemyClass->agility);
				$enemy->setAttribute('currentAgility', $enemyRace->agility + $enemyClass->agilty);
				$enemy->setAttribute('currentDefense', $enemyClass->defense + $additionalDefense);
				$enemy->setAttribute('defense', $enemyClass->defense);
				$enemy->setAttribute('armour', $additionalArmour);
				$enemy->setAttribute('accuracy', $enemyClass->accuracy);
				$enemy->setAttribute('currentAccuracy', $enemyClass->accuracy);
				$enemy->setAttribute('baseAttackCost', $enemyClass->baseAttackCost);
				$enemy->setAttribute('avatar', $enemyRace->avatar);
				$enemy->setAttribute('meleeAnimation', $enemyRace->meleeAnimation);
				$enemy->setAttribute('attack', $enemyRace->attack + $enemyClass->attack + $strengthAlloc);
				$enemy->setAttribute('currentAttack', $enemyRace->attack + $enemyClass->attack + $strengthAlloc + $additionalAttack);
				$enemy->setAttribute('weapon', $enemyChoices[$enemyChoice]->weapon);
				$enemy->setAttribute('offHand', $enemyChoices[$enemyChoice]->offHand);
				$enemy->setAttribute('bodyEquipment', $enemyChoices[$enemyChoice]->bodyEquipment);
				$enemy->setAttribute('headEquipment', $enemyChoices[$enemyChoice]->headEquipment);
				$enemy->setAttribute('armsEquipment', $enemyChoices[$enemyChoice]->armsEquipment);
				$enemy->setAttribute('legsEquipment', $enemyChoices[$enemyChoice]->legsEquipment);
				$enemy->setAttribute('money', $enemyChoices[$enemyChoice]->money);
				
				$existingMap->enemies()->save($enemy);
				
				//grants lootable inventory item
				
				$enemyLootableItem = $enemyChoices[$enemyChoice]->itemLootInventory;
				$targetItem = GameItem::where('name', $enemyLootableItem[0]['name'])->first();
				$enemyItem = new GameActiveEnemyItem();
				$enemyItem->setAttribute('itemId', $targetItem->id);
				$enemyItem->setAttribute('ownerId', $enemy->id);
				$enemyItem->setAttribute('quantity', 1);
				$enemy->items()->save($enemyItem);
				
				//save enemy id to tileset
				$mapDecoded[$randEnemyPosition[0]][$randEnemyPosition[1]]['enemy'] = strval($enemy->id);
			}
			
			//returns coordinates only for map generator
			$filteredEnemies = $existingMap->enemies()->get()->pluck('mapPosition');
			
			//save updated tileset with enemies
			//Log::debug($mapDecoded);
			$tileSet = $existingMap->tileset()->first();
			$tileSet->mapData = $mapDecoded;
			$existingMap->tileset()->save($tileSet);
			
			//assign turn number based on agility, sets current turn to one
			$charObj->gameTurns = $gameLevel + 1;
			$charObj->currentTurn = 1;
			$actors = $existingMap->enemies()->get();
			$actors->push($charObj);
			$sortAgiDesc = $actors->sortBy([['currentAgility', 'desc']]);
			$turnNumber = 1;
			foreach ($sortAgiDesc as $actor) {
				$actor->turnPosition = $turnNumber;
				$turnNumber = $turnNumber + 1;
				$actor->save();
			}

			return response(['enemies' => $filteredEnemies], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Character could not be created. Please report to admin.'], 422);
		}	
	}
	
	public function getEnemies(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			//returns coordinates only for map generator
			$filteredEnemies = $existingMap->enemies()->get()->map->only('mapPosition', 'avatar', 'currentHealth');
			//return response(['enemies' => $existingMap->enemies()->get()], 200);
			return response(['enemies' => $filteredEnemies], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}		
	}

	public function inspectEnemies(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$charRow = $charObj->mapPosition[0];
			$charColumn = $charObj->mapPosition[1];
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			
			//$enemies = $existingMap->enemies()->get();
			//returns coordinates only for map generator
			//$enemies = $existingMap->enemies()->get()->pluck('attack', 'currentAttack', 'health', 'currentHealth', 'stamina', 			//'currentStamina', 'mapPosition');
			
			$enemies = $existingMap->enemies()->get([
				'name', 'attack', 'currentAttack', 'health',
				'currentHealth', 'stamina', 'currentStamina', 'mapPosition', 'armour'
			]);
			
			$inspectableTargets = array();

			$observedSquares = array(
				'northwest' => [$charRow - 1, $charColumn - 1],
				'north' => [$charRow - 1, $charColumn],
				'northeast' => [$charRow - 1, $charColumn + 1],
				'west' => [$charRow, $charColumn - 1],
				'east' => [$charRow, $charColumn + 1],
				'southwest' => [$charRow + 1, $charColumn - 1],
				'south' => [$charRow + 1, $charColumn],
				'southeast' => [$charRow + 1, $charColumn + 1],
			);
			
			foreach ($observedSquares as $key => $val) {
				foreach($enemies as $enemy => $e) {
					if($e->mapPosition === $val) {
						$e->mapOrientation = $key;
						array_push($inspectableTargets, $e);
						$enemies->forget((string)$enemy);
					}
				}
			}
			if(empty($inspectableTargets))
				return response(['message' => 'No enemies nearby.', 'squares' => $observedSquares, 'enemies' => $inspectableTargets], 200);
			else
				return response(['message' => 'Inspection complete.', 'squares' => $observedSquares, 'enemies' => $inspectableTargets], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}		
	}
	
	public function inspectBattleEnemy(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$enemy = GameActiveEnemy::where('id', $charObj->enemyId)->first();
			//['name', 'attack', 'currentAttack', 'health',
			//	'currentHealth', 'stamina', 'currentStamina', 'mapPosition'
			//]);
			
			//Log::debug($enemy); 
			
			return response(['enemy' => $enemy], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}		
	}
	
	//enemy decides on a move action
	public function gameEnemyTurnDecision(Request $request) 
	{
		try {
			//gets data for user
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			
			//gets map tileset as array for placing enemy data
			$map = $existingMap->tileset()->first()->pluck('mapData');
			$mapDecoded = json_decode($map[0], TRUE);
			
			//enemy turn positions for component response
			$enemiesTurnPositions = $existingMap->enemies()->get()->pluck('id', 'turnPosition');
			
			//current enemy acting in this function, using request data
			$enemy = $existingMap->enemies()->get()->where('id', $request->currentEnemyActing)->first();
			
			//if enemy is dead
			if($enemy->currentHealth <= 0) {
				$charObj->currentTurn = $charObj->currentTurn + 1;
				if($charObj->currentTurn > $charObj->gameTurns)
					$charObj->currentTurn = 1;
				$charObj->save();
				return response([
					'currentTurn' => $charObj->currentTurn,
					'playerTurnPosition' => $charObj->turnPosition,
					'playerGameTurns' => $charObj->gameTurns,
					'playerBattleState' => $charObj->battle,
					'playerBattleTarget' => $charObj->enemyId,
					'enemyTurnPositions' => $enemiesTurnPositions,
					'enemyAction' => 'Dead',
					'enemyOldPosition' => $enemy->mapPosition,
					'enemyNewPosition' => null,
					'enemyId' => $enemy->id,
					'enemyLastTerrain' => null,
					'enemyLastTerrainTreeCover' => null,
					'enemyAvatar' => $enemy->avatar,
					'enemyName' => $enemy->name,
				], 200);	
			}
			
			//validates that this enemy should be acting
			if($enemy->turnPosition != $charObj->currentTurn) {
				$enemy = $existingMap->enemies()->get()->where('turnPosition', $charObj->currentTurn)->first();
				//if no enemy found matching turn, turn is incremented and returns to component 
				if(!$enemy) {
					//updates turn numbers stored in character table
					if($charObj->currentTurn == $charObj->gameTurns)
						$charObj->currentTurn = 1;		
					else
						$charObj->currentTurn = $charObj->currentTurn + 1;
					$charObj->save();			
					return response([
						'currentTurn' => $charObj->currentTurn,
						'playerTurnPosition' => $charObj->turnPosition,
						'playerGameTurns' => $charObj->gameTurns,
						'playerBattleState' => $charObj->battle,
						'playerBattleTarget' => $charObj->enemyId,
						'enemyTurnPositions' => $enemiesTurnPositions,
						//'enemyAction' => $enemy->turnAction,
						//'enemyOldPosition' => [$enemyRow, $enemyColumn],
						//'enemyNewPosition' => $enemy->mapPosition,
						//'enemyId' => $enemy->id,
					], 200);
				}
			}	
			
			//updates turn numbers stored in character table
			//if($charObj->currentTurn == $charObj->gameTurns)
			//	$charObj->currentTurn = 1;		
			//else
			//	$charObj->currentTurn = $charObj->currentTurn + 1;
		
			//add code to decide what enemy does
			
			//$enemy->turnAction = ['action' => 'nothing']; returns nothing to mounted in component
			
			//first finds distance to player
			$charRow = $charObj->mapPosition[0];
			$charColumn = $charObj->mapPosition[1];
			
			$enemyRow = $enemy->mapPosition[0];
			$enemyColumn = $enemy->mapPosition[1];
			
			$distance = sqrt((($enemyRow - $charRow) ** 2) + (($enemyColumn - $charColumn) ** 2));
			$decimalDistance = floor($distance);
			$fractionDistance = $distance - $decimalDistance;
			$finalDistance = 0;
			if($fractionDistance < .5)
				$finalDistance = floor($distance);
			else
				$finalDistance = ceil($distance);
			
			//with distance found, compares to range to see if in range for attack if not moves, uses skill or item, etc
			if($enemy->combatRange >= $finalDistance) {
				$enemy->turnAction = ['action' => 'attack'];
			}
			else if($enemy->combatRange < $finalDistance) {
				$enemy->turnAction = ['action' => 'move'];
				
				//depending on which random number used, enemy will prefer a row, column or diagonal move
				$movementType = rand(0, 2);
				//move closer by column
				if($movementType == 0) {
					switch($enemyColumn) {
						//on left of enemy
						case($enemyColumn > $charColumn):
							$enemy->mapPosition = [$enemyRow, $enemyColumn - 1];
							break;
						//on right of enemy
						case($enemyColumn < $charColumn):
							$enemy->mapPosition = [$enemyRow, $enemyColumn + 1];
							break;
						//above enemy
						case($enemyColumn == $charColumn && $charRow < $enemyRow):
							$enemy->mapPosition = [$enemyRow - 1, $enemyColumn];
							break;
						//below enemy
						case($enemyColumn == $charColumn && $charRow > $enemyRow):
							$enemy->mapPosition = [$enemyRow + 1, $enemyColumn];
							break;
						default:
							break;
					}	
				}	
				//move closer by row
				else if($movementType == 1){
					switch($enemyRow) {
						//above enemy
						case($enemyRow > $charRow):
							$enemy->mapPosition = [$enemyRow - 1, $enemyColumn];
							break;
						//below enemy
						case($enemyRow < $charRow):
							$enemy->mapPosition = [$enemyRow + 1, $enemyColumn];
							break;
						//same row to right
						case($enemyRow == $charRow && $enemyColumn < $charColumn):
							$enemy->mapPosition = [$enemyRow, $enemyColumn + 1];
							break;
						//same row to left
						case($enemyRow == $charRow && $enemyColumn > $charColumn):
							$enemy->mapPosition = [$enemyRow, $enemyColumn - 1];
							break;
						default:
							break;
					}
				}
				//move diagonally
				else {
					switch([$enemyRow, $enemyColumn]) {
						//north of enemy
						case($enemyRow > $charRow && $enemyColumn == $charColumn):
							$enemy->mapPosition = [$enemyRow - 1, $enemyColumn];
							break;
						//south of enemy
						case($enemyRow < $charRow && $enemyColumn == $charColumn):
							$enemy->mapPosition = [$enemyRow + 1, $enemyColumn];
							break;
						//NW of enemy
						case($enemyRow > $charRow && $enemyColumn > $charColumn):
							$enemy->mapPosition = [$enemyRow - 1, $enemyColumn - 1];
							break;
						//NE of enemy
						case($enemyRow > $charRow && $enemyColumn < $charColumn):
							$enemy->mapPosition = [$enemyRow - 1, $enemyColumn + 1];
							break;
						//SW of enemy
						case($enemyRow < $charRow && $enemyColumn > $charColumn):
							$enemy->mapPosition = [$enemyRow + 1, $enemyColumn - 1];
							break;
						//SE of enemy
						case($enemyRow < $charRow && $enemyColumn < $charColumn):
							$enemy->mapPosition = [$enemyRow + 1, $enemyColumn + 1];
							break;
						//to left of enemy
						case($enemyRow == $charRow && $enemyColumn > $charColumn):
							$enemy->mapPosition = [$enemyRow, $enemyColumn - 1];
							break;
						//to right of enemy
						case($enemyRow == $charRow && $enemyColumn < $charColumn):
							$enemy->mapPosition = [$enemyRow, $enemyColumn + 1];
							break;	
						default:
							break;
					}
				}
				
				if($enemy->mapPosition == [$charRow, $charColumn])
					$enemy->mapPosition = [$enemyRow, $enemyColumn];
				
				//increments turn number and save
				$charObj->currentTurn = $charObj->currentTurn + 1;
				if($charObj->currentTurn > $charObj->gameTurns)
					$charObj->currentTurn = 1;
			}
			else {
				$enemy->turnAction = ['action' => 'nothing'];
			}
			
			//gets terrain data of square enemy was standing on and sends back to controller
			$map = $existingMap->tileset()->first()->pluck('mapData');
			$mapDecoded = json_decode($map[0], TRUE);
			//Log::debug($mapDecoded[$enemyRow][$enemyColumn]);
			$enemyLastTerrain = $mapDecoded[$enemyRow][$enemyColumn]['terrain'];
			$enemyLastTerrainTreeCover = $mapDecoded[$enemyRow][$enemyColumn]['treeCover'];
			
			//removes enemy id from last square it was on in tileset
			$mapDecoded[$enemyRow][$enemyColumn]['enemy'] = "";
			
			//saves enemy id to new square
			$mapDecoded[$enemy->mapPosition[0]][ $enemy->mapPosition[1]]['enemy'] = strval($enemy->id);

			//saves updated tileset, character and enemy
			$tileSet = $existingMap->tileset()->first();
			$tileSet->mapData = $mapDecoded;
			$existingMap->tileset()->save($tileSet);
			$enemy->save();
			//$charObj->currentTurn = $charObj->currentTurn + 1;
			//if($charObj->currentTurn > $charObj->gameTurns)
			//	$charObj->currentTurn = 1;
			$charObj->save();
			
			return response([
				'currentTurn' => $charObj->currentTurn,
				'playerTurnPosition' => $charObj->turnPosition,
				'playerGameTurns' => $charObj->gameTurns,
				'playerBattleState' => $charObj->battle,
				'playerBattleTarget' => $charObj->enemyId,
				'enemyTurnPositions' => $enemiesTurnPositions,
				'enemyAction' => $enemy->turnAction,
				'enemyOldPosition' => [$enemyRow, $enemyColumn],
				'enemyNewPosition' => $enemy->mapPosition,
				'enemyId' => $enemy->id,
				'enemyLastTerrain' => $enemyLastTerrain,
				'enemyLastTerrainTreeCover' => $enemyLastTerrainTreeCover,
				'enemyAvatar' => $enemy->avatar,
			], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Turn list could not be made. Please report to admin.'], 422);
		}			
	}	
}
?>