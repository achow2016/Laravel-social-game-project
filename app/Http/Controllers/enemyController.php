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

	public function generateEnemies(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			
			if (!$user) {
				throw ValidationException::withMessages([
					'message' => ['database error, user does not exist.'],
				]);
			}
			
			$charObj = $user->character()->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			$gameLevel = $charObj->gameLevel;
			$enemyChoices = GameEnemy::where('gameLevel', $gameLevel)->get();
			$enemyChoicesCount = $enemyChoices->count();
			
			$usedMapPositions = array($charObj->mapPosition);
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
						$usedMapPositions += $randEnemyPosition;
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
			
			return response(['enemies' => $existingMap->enemies()->get()], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Character could not be created. Please report to admin.'], 422);
		}	
	}

	public function getCharacterStatus(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$character = $user->character()->first();
			if($character) {
				return response(['character' => $character], 200);
			}
			else {
				return response(['status' => 'Error, your character could not be found. Please report to admin.'], 422);
			}	
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Character could not be created. Please report to admin.'], 422);
		}
	}	
}
?>