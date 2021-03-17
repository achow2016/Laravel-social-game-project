<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
use App\Models\Character;
use App\Models\CharacterRace;
use App\Models\CharacterClass;
use App\Models\User;
use App\Models\GameMap;
use App\Models\GameMapTileset;

//use DateTime;

class CharacterController extends Controller {

	public function createCharacter(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();

			//deletes old character and map data if any
			$charCheck = $user->character()->first();
			
			if($charCheck) {
				$gameMap = GameMap::where('id', $charCheck->mapId)->first();
				if($gameMap) {
					$gameMap->tileset()->first()->delete();
					$gameMap->delete();
				}
				$user->character()->delete();
			}
			
			$request->validate([
				'username' => 'required',
				'characterName' => 'required|unique:character',
				'strengthAlloc' => 'required|numeric|max:12',
				'enduranceAlloc' => 'required|numeric|max:12',
				'lifeAlloc' => 'required|numeric|max:12',
				'totalAlloc' => 'required|numeric|max:12',
				'gameRace' => 'required',
				'gameClass' => 'required',
			]);
			
			$characterRace = CharacterRace::where('race', $request->gameRace)->first();
			$characterClass = CharacterClass::where('name', $request->gameClass)->first();

					
				
			$character = new Character();
			$character->setAttribute('raceId', $characterRace->id);
			$character->setAttribute('classId', $characterClass->id);
			$character->setAttribute('ownerUser', $request->user()->id);
			$character->setAttribute('characterName', $request->characterName);
			$character->setAttribute('health', $characterRace->health + $request->lifeAlloc);
			$character->setAttribute('currentHealth', $characterRace->health + $request->lifeAlloc);
			$character->setAttribute('healthRegen', $characterRace->healthRegen);
			$character->setAttribute('currentHealthRegen', $characterRace->healthRegen);
			$character->setAttribute('stamina', $characterRace->stamina  + $request->enduranceAlloc);
			$character->setAttribute('currentStamina', $characterRace->stamina  + $request->enduranceAlloc);
			$character->setAttribute('staminaRegen', $characterRace->staminaRegen);
			$character->setAttribute('currentStaminaRegen', $characterRace->staminaRegen);
			$character->setAttribute('agility', $characterRace->agility);
			$character->setAttribute('currentAgility', $characterRace->agility);
			$character->setAttribute('attack', $characterRace->attack  + $request->strengthAlloc);
			$character->setAttribute('currentAttack', $characterRace->attack  + $request->strengthAlloc);
			$user->character()->save($character);
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
	
	public function getCharacterBattleStatus(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$character = $user->character()->first();
			if($character) {
				//return response(['character' => $character], 200);
				if($character->battle == true) {
					$enemy = $character->currentEnemy()->first();
					return response(['battleStatus' => true, 'distance' => $character->engageDistance, 'enemy' => $enemy], 200);
				}
				else
					return response(['battleStatus' => false], 200);
			}
			else {
				return response(['status' => 'Error, your character could not be found. Please report to admin.'], 422);
			}	
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Character battle status could not be determined. Please report to admin.'], 422);
		}
	}
	
	public function getCharacterExistenceStatus(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$character = $user->character()->first();
			if(!$character->isEmpty()) {
				return response(['characterStatus' => true], 200);
			}
			else {
				return response(['characterStatus' => false], 200);
			}	
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Character status could not be determined. Please report to admin.'], 422);
		}
	}

	//gets enemy detail, distance to enemy and moves to battle component
	public function fightEnemy(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$charRow = $charObj->mapPosition[0];
			$charColumn = $charObj->mapPosition[1];
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			
			/*
			applies damage, can be used for item use in map
			
			$mapCoord = explode(",", $request->input('mapPosition'));
			$enemy = $existingMap->enemies()->get()->where('mapPosition', $mapCoord)->first();
			$playerDamage = $charObj->currentAttack * $charObj->attackMultiplier;
			$enemy->currentHealth = $enemy->currentHealth - $playerDamage;
			//Log::debug($enemy->currentHealth); 
			$existingMap->enemies()->save($enemy);
			
			return response(['enemy' => $enemy, 'playerDamage' => $playerDamage], 200);
			*/
			
			$enemyMapCoord = explode(",", $request->input('mapPosition'));
			$enemy = $existingMap->enemies()->get()->where('mapPosition', $enemyMapCoord)->first();
			
			$enemyRow = $enemyMapCoord[0];
			$enemyColumn = $enemyMapCoord[1];
			$distance = sqrt((($enemyRow - $charRow) ** 2) + (($enemyColumn - $charColumn) ** 2));
			$decimalDistance = floor($distance);
			$fractionDistance = $distance - $decimalDistance;
			$finalDistance = 0;
			if($fractionDistance < .5)
				$finalDistance = floor($distance);
			else
				$finalDistance = ceil($distance);
			
			if(!$enemy) {
				return response(['error' => 'No enemy found on square.'], 200);
			}	
			
			$charObj->battle = true;
			$charObj->enemyId = $enemy->id;
			$charObj->engageDistance = $finalDistance;
			$charObj->save();
			

			
			
			return response(['distance' => $finalDistance, 'enemy' => $enemy], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}		
	}		
	
	
}
?>