<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
use App\Models\Character;
use App\Models\CharacterRace;
use App\Models\CharacterClass;
use App\Models\GameEnemy;
use App\Models\GameActiveEnemy;
use App\Models\User;
use App\Models\GameMap;
use App\Models\GameMapTileset;

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
			
			//$mapGrid = array_fill(0, 8, array_fill(0, 8, 0));
			//$mapGrid[$charObj->mapPosition[0]][$charObj->mapPosition[1]] = 1;
			
			//Log::debug($mapGrid); player placed on matching 2d array
			
			for($i = 0; $i < $gameLevel; $i++) {
				$enemyChoice = max((rand(0,$enemyChoicesCount - 1)), 0 );
				$strengthAlloc = rand(0, 12);
				$enduranceAlloc = max((rand(0, 12) - $strengthAlloc), 0);
				$lifeAlloc = max((rand(0, 12) - $strengthAlloc - $enduranceAlloc), 0);
				
				$enemyRace = CharacterRace::where('race', $enemyChoices[$enemyChoice]->gameRace)->first();
				$enemyClass = CharacterClass::where('name', $enemyChoices[$enemyChoice]->gameClass)->first();			
				
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
				
				$enemy->setAttribute('raceId', $enemyRace->id);
				$enemy->setAttribute('classId', $enemyClass->id);
				$enemy->setAttribute('name', $enemyChoices[$enemyChoice]->name);
				$enemy->setAttribute('health', $enemyRace->health + $lifeAlloc);
				$enemy->setAttribute('currentHealth', $enemyRace->health + $lifeAlloc);
				$enemy->setAttribute('healthRegen', $enemyRace->healthRegen);
				$enemy->setAttribute('currentHealthRegen', $enemyRace->healthRegen);
				$enemy->setAttribute('stamina', $enemyRace->stamina  + $enduranceAlloc);
				$enemy->setAttribute('currentStamina', $enemyRace->stamina  + $enduranceAlloc);
				$enemy->setAttribute('staminaRegen', $enemyRace->staminaRegen);
				$enemy->setAttribute('currentStaminaRegen', $enemyRace->staminaRegen);
				$enemy->setAttribute('agility', $enemyRace->agility);
				$enemy->setAttribute('currentAgility', $enemyRace->agility);
				$enemy->setAttribute('attack', $enemyRace->attack  + $strengthAlloc);
				$enemy->setAttribute('currentAttack', $enemyRace->attack  + $strengthAlloc);
				$existingMap->enemies()->save($enemy);
			}
			
			//returns coordinates only for map generator
			$filteredEnemies = $existingMap->enemies()->get()->pluck('mapPosition');
			
			//return response(['enemies' => $existingMap->enemies()->get()], 200);
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
			$filteredEnemies = $existingMap->enemies()->get()->pluck('mapPosition');
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
				'currentHealth', 'stamina', 'currentStamina', 'mapPosition'
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
			return response(['squares' => $observedSquares, 'enemies' => $inspectableTargets], 200);
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
			
			Log::debug($enemy); 
			
			return response(['enemy' => $enemy], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}		
	}
}
?>