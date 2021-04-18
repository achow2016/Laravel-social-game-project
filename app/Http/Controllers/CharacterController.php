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
use App\Models\GameActiveEnemy;
//use DateTime;

use App\Traits\GameTurnLogic;

class CharacterController extends Controller {
	
	use GameTurnLogic;

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
			$character->setAttribute('health', $characterRace->health + $characterClass->health + $request->lifeAlloc);
			$character->setAttribute('currentHealth', $characterRace->health + $characterClass->health + $request->lifeAlloc);
			$character->setAttribute('healthRegen', $characterRace->healthRegen + $characterClass->healthRegen);
			$character->setAttribute('currentHealthRegen', $characterRace->healthRegen + $characterClass->healthRegen);
			$character->setAttribute('stamina', $characterRace->stamina + $characterClass->stamina + $request->enduranceAlloc);
			$character->setAttribute('currentStamina', $characterRace->stamina + $characterClass->stamina + $request->enduranceAlloc);
			$character->setAttribute('staminaRegen', $characterRace->staminaRegen + $characterClass->staminaRegen);
			$character->setAttribute('currentStaminaRegen', $characterRace->staminaRegen + $characterClass->staminaRegen);
			$character->setAttribute('agility', $characterRace->agility + $characterClass->agility);
			$character->setAttribute('currentAgility', $characterRace->agility + $characterClass->agility);
			$character->setAttribute('defense', $characterClass->defense);
			$character->setAttribute('currentDefense', $characterClass->defense);
			$character->setAttribute('accuracy', $characterClass->accuracy);
			$character->setAttribute('currentAccuracy', $characterClass->accuracy);
			$character->setAttribute('baseAttackCost', $characterClass->baseAttackCost);
			$character->setAttribute('attack', $characterRace->attack + $characterClass->attack + $request->strengthAlloc);
			$character->setAttribute('currentAttack', $characterRace->attack + $characterClass->attack + $request->strengthAlloc);
			$character->setAttribute('avatar', $characterRace->avatar);
			$character->setAttribute('meleeAnimation', $characterRace->meleeAnimation);
			$character->setAttribute('money', 0);
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
					$charRow = $character->mapPosition[0];
					$charColumn = $character->mapPosition[1];					
					$enemy = $character->currentEnemy()->first();
					$enemyMapCoord = $enemy->mapPosition;
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
					
					return response(['battleStatus' => true, 'player' => $character,  'enemy' => $enemy, 'distance' => $finalDistance], 200);
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

	//on switching to fight mode
	public function switchFight(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();			
			$mapCoordArray = explode(",", $request->mapPosition);
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			$enemyObj = $existingMap->enemies()->get()->where('mapPosition', $mapCoordArray)->first();
			$charObj->enemyId = $enemyObj->id;
			$charObj->battle = true;
			$charObj->save();
			return response(['status' => 'Combat target and state saved.'], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}
	}

	//melee selected in battle component
	public function startFight(Request $request) 
	{
		try {
			//return $this->findBattlePhaseOrder($request);
			$results = $this->findBattleTurnOrder($request);
			return response(['results' => $results], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}
	}
	//gets enemy detail, distance to enemy and moves to battle component to fight targeted enemy
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
	
	//goes to trait to determine exchange results
	public function meleeEnemy(Request $request) 
	{
		//return $this->findBattleTurnOrder($request);
		$results = $this->findBattleTurnOrder($request);
		return response(['results' => $results], 200);
	}

	//gets character turn list
	public function getTurnList(Request $request) 
	{
		try {		
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$currentTurn = $charObj->currentTurn;
			$playerTurnPosition = $charObj->turnPosition;
			
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			$enemiesTurnOrder = $existingMap->enemies()->get()->pluck('id', 'turnPosition');
			
			return response(['playerTurnPosition' => $playerTurnPosition, 'enemiesTurnOrder' => $enemiesTurnOrder], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Turn list could not be made. Please report to admin.'], 422);
		}			
	}
	
	//gets game state
	public function getGameState(Request $request) 
	{
		try {		
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			$enemiesTurnPositions = $existingMap->enemies()->get()->pluck('id', 'turnPosition');
			
			if($charObj->battle == true) {
				$enemy = $existingMap->enemies()->get()->where('id', $charObj->enemyId)->first();
				return response([
					'currentTurn' => $charObj->currentTurn,
					'playerAvatar' => $charObj->avatar,
					'playerTurnPosition' => $charObj->turnPosition,
					'playerGameTurns' => $charObj->gameTurns,
					'playerBattleState' => $charObj->battle,
					'playerBattleTarget' => $charObj->enemyId,
					'enemyTurnPositions' => $enemiesTurnPositions,
					'currentEnemyMapCoord' => $enemy->mapPosition
				], 200);
			}	
			else {
				return response([
					'currentTurn' => $charObj->currentTurn,
					'playerAvatar' => $charObj->avatar,
					'playerTurnPosition' => $charObj->turnPosition,
					'playerGameTurns' => $charObj->gameTurns,
					'playerBattleState' => $charObj->battle,
					'playerBattleTarget' => $charObj->enemyId,
					'enemyTurnPositions' => $enemiesTurnPositions
				], 200);
			}	
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'game state data list could not be made. Please report to admin.'], 422);
		}
	}	
}
?>