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
use App\Models\ActiveGameCharacterItem;
use App\Models\GameItem;
use App\Models\GameWeapon;
use App\Models\GameOffhand;
use App\Models\GameLegEquipment;
use App\Models\GameArmsEquipment;
use App\Models\GameHeadEquipment;
use App\Models\GameBodyEquipment;
use App\Models\GameScoreRecord;



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
				$charCheck->items()->delete();
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
			$characterClass = CharacterClass::where('class', $request->gameClass)->first();
			
			$character = new Character();
			$character->setAttribute('race', $characterRace->race);
			$character->setAttribute('class', $characterClass->class);
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
			$character->setAttribute('currentAttack', $characterRace->attack + $characterClass->attack + $request->strengthAlloc + 100);
			$character->setAttribute('avatar', $characterRace->avatar);
			$character->setAttribute('meleeAnimation', $characterRace->meleeAnimation);
			$character->setAttribute('money', 0);
			$user->character()->save($character);
		
			//grants starter healing item
			$aidKit = GameItem::where('name', 'Aid Kit')->first();
			$starterHealingItem = new ActiveGameCharacterItem();
			$starterHealingItem->setAttribute('itemId', $aidKit->id);
			$starterHealingItem->setAttribute('ownerId', $character->id);
			$starterHealingItem->setAttribute('quantity', 1);
			$character->items()->save($starterHealingItem);
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

	public function getCharacterInventory(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$character = $user->character()->first();
			if($character) {			
				$characterInventory = $character->items()->get();
				$characterActualItems = [];
				foreach($characterInventory as $item) {
					$originalItemToAdd = GameItem::where('id', $item->itemId)->first();
					$originalItemToAdd->quantity = $item->quantity;
					$characterActualItems[] = $originalItemToAdd;
				}
				return response(['characterInventory' => $characterActualItems], 200);
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
			//$enemyObj = $existingMap->enemies()->get()->where('mapPosition', $mapCoordArray)->first();
			$enemyObj = GameActiveEnemy::where('mapId', $existingMap->id)->get()->where('mapPosition', $mapCoordArray)->first();
			if(!$enemyObj)
				return response(['message' => 'Invalid combat target.'], 200);
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

	//melee selected in battle component, effects updated after battle
	public function startFight(Request $request) 
	{
		try {
			$results = $this->findBattleTurnOrder($request);
			$effectsUpdates = $this->updateEffects($request);
			foreach($effectsUpdates as $update) {
				$results = $results['message'] . $update;
			}
			return response(['results' => $results], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}
	}
	
	//advance game to next level
	public function nextLevel(Request $request) 
	{
		try {
			$results = $this->advanceLevel($request);
			return response(['result' => $results], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}
	}
	
	//use item, game code in game turn logic, effects updated after item use
	public function useItem(Request $request) 
	{
		try {
			//$results = $this->useItem($request);
			$results = $this->usePlayerItem($request);
			$effectsUpdates = $this->updateEffects($request);
			foreach($effectsUpdates as $update) {
				$results = $results['message'] . $update;
			}
			return response(['results' => $results], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}
	}
	
	//picks up item left on map square
	public function lootEnemy(Request $request) 
	{
		try {
			$results = $this->pickUpEnemyLoot($request);
			return response(['results' => $results], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Loot could not be found. Please report to admin.'], 422);
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
	
	//gets game character avatar
	public function getAvatar(Request $request) 
	{
		try {		
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			
			return response([
				'playerAvatar' => $charObj->avatar
			], 200);
			
		}
		catch(Throwable $e) {
			report($e);
			return response(['message' => 'game state data list could not be made. Please report to admin.'], 422);
		}
	}
	
	//gets game scores
	public function getScores(Request $request) 
	{
		try {		
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$scores = GameScoreRecord::select('userName', 'characterName', 'score')->get();
			
			return response([
				'scores' => $scores
			], 200);
			
		}
		catch(Throwable $e) {
			report($e);
			return response(['message' => 'game score data list could not be made. Please report to admin.'], 422);
		}
	}
	
	//gets game state, calls set game clear if enemy turn positions return no healthy enemies
	public function getGameState(Request $request) 
	{
		try {		
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			//$enemiesTurnPositions = $existingMap->enemies()->get()->pluck('id', 'turnPosition');
			//$enemiesTurnPositions = $existingMap->enemies()->select('id', 'turnPosition', 'currentHealth')->get();
			$enemiesTurnPositions = GameActiveEnemy::where('mapId', $existingMap->id)->select('id', 'turnPosition', 'currentHealth')->get();
			
			//set level to complete on check
			if(!$charObj->mapComplete) {
				$enemyHealthy = false;
				foreach($enemiesTurnPositions as $enemy) {
					if($enemy->currentHealth > 0)
						$enemyHealthy = true;
				}
				if(!$enemyHealthy) {
					$charObj->mapComplete = true;
					$charObj->save();
				}
			}
	
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
	
	//quits game, save score
	public function quitGame(Request $request) 
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$charObj = $user->character()->first();			
			
			//delete previous record if any
			$score = GameScoreRecord::where('userId', $user->id)->get();
			if($score->first()) {
				$score->first()->delete();
			}
			
			//save score
			$score = new GameScoreRecord();
			$score->setAttribute('gameLevel', $charObj->gameLevel);
			$score->setAttribute('race', $charObj->race);
			$score->setAttribute('class', $charObj->class);
			$score->setAttribute('userName', $request->user()->name);
			$score->setAttribute('userId', $request->user()->id);
			$score->setAttribute('characterName', $charObj->characterName);
			$score->setAttribute('avatar', $charObj->avatar);
			$score->setAttribute('health', $charObj->health);
			$score->setAttribute('healthRegen', $charObj->healthRegen);
			$score->setAttribute('stamina', $charObj->stamina);
			$score->setAttribute('staminaRegen', $charObj->staminaRegen);
			$score->setAttribute('agility', $charObj->agility);
			$score->setAttribute('defense', $charObj->defense);
			$score->setAttribute('accuracy', $charObj->accuracy);
			$score->setAttribute('attack', $charObj->attack);
			$score->setAttribute('armour', $charObj->armour);
			$score->setAttribute('money', $charObj->money);
			$score->setAttribute('weapon', $charObj->weapon);
			$score->setAttribute('offHandEquipment', $charObj->offHandEquipment);
			$score->setAttribute('damageDealt', $charObj->damageDealt);
			$score->setAttribute('damageReceived', $charObj->damageReceived);
			$score->setAttribute('itemsUsed', $charObj->itemsUsed);
			$score->setAttribute('enemiesKilled', $charObj->enemiesKilled);
			$score->setAttribute('squaresMoved', $charObj->squaresMoved);
			$score->setAttribute('totalEarnings', $charObj->totalEarnings);
			$score->setAttribute('score', $charObj->score);
			$score->setAttribute('bodyEquipment', $charObj->bodyEquipment);
			$score->setAttribute('headEquipment', $charObj->headEquipment);
			$score->setAttribute('armsEquipment', $charObj->armsEquipment);
			$score->setAttribute('legsEquipment', $charObj->legsEquipment);
			$user->score()->save($score);
			
			//clean up map
			$existingMap = GameMap::where('id', $charObj->mapId)->first();
			GameActiveEnemy::where('mapId', $existingMap->id)->delete();
			$tileCheck = $existingMap->tileset()->first();
			if($tileCheck) 
				$existingMap->tileset()->delete();
			$existingMap->delete();
			$user->character()->delete();
			
			return response(['message' => 'Quit game, deleted character and map.'], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'enemies could not be found. Please report to admin.'], 422);
		}
	}
}
?>