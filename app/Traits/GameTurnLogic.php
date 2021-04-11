<?php 
namespace App\Traits;

use App\Models\Character;
use App\Models\User;
use App\Models\GameMap;
use App\Models\GameActiveEnemy;

use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

trait GameTurnLogic
{
	private $playerTurnOrder;
	
	/*
	//returns array of key value pairs of index of enemy and player in current agility descending order
	public function findBattlePhaseOrder(Request $request)     
	{               
		$user = User::where('name', $request->user()->name)->first();
		$charObj = $user->character()->first();
		$charObj->battle = true;
		$charObj->save();
		
		$charObjSpeed = $charObj->currentAgility;
		$existingMap = GameMap::where('id', $charObj->mapId)->first();
		$enemyObjs = $existingMap->enemies()->get();
		$enemyObjsSpeed = $enemyObjs->pluck('currentAgility');
		
		//$actorArray[] = array();
		$actorArray['player'] = $charObjSpeed;
		for($i = 0; $i < count($enemyObjsSpeed); $i++) {
			$actorArray['enemy' . $i] = $enemyObjsSpeed[$i];
		}	
		arsort($actorArray);
		//return $actorArray;
		return response(['actorArray' => $actorArray], 200);
	}
	*/
	
	//returns array of key value pairs of index of enemy and player in current agility descending order
	public function findMoveTurnOrder(Request $request)     
	{               
		$user = User::where('name', $request->user()->name)->first();
		$charObj = $user->character()->first();
		$charObjSpeed = $charObj->currentAgility;
		$existingMap = GameMap::where('id', $charObj->mapId)->first();
		$enemyObjsSpeed = $existingMap->enemies()->get()->pluck('currentAgility');
		
		$actorArray[] = array();
		$actorArray['player'] = $charObjSpeed;
		for($i = 0; $i < count($enemyObjsSpeed); $i++) {
			$actorArray['enemy' . $i] = $enemyObjsSpeed[$i];
		}	
		arsort($actorArray);
		return $actorArray;
	}
	
	public function findBattleTurnOrder(Request $request)     
	{               
		$user = User::where('name', $request->user()->name)->first();
		$charObj = $user->character()->first();
		
		$mapCoordArray = explode(",", $request->mapPosition);
		$existingMap = GameMap::where('id', $charObj->mapId)->first();
		$enemyObj = $existingMap->enemies()->get()->where('mapPosition', $mapCoordArray)->first();
		if($enemyObj) {
			$charObj->enemyId = $enemyObj->id;
		}	
		else
			$enemyObj = $existingMap->enemies()->get()->where('id', $charObj->enemyId)->first();
	
		$charObj->battle = true;
		$charObj->save();
	
	
		if($charObj->currentAgility < $enemyObj->currentAgility) {
			$this->playerTurnOrder = 'second';
			
			//add code to decide if skill, item or melee from enemy later
			return $this->exchangeMelee($request);
		}
		else if($charObj->currentAgility > $enemyObj->currentAgility) {
			$this->playerTurnOrder = 'first';
			
			//add code to decide if skill, item or melee from enemy later			
			return $this->exchangeMelee($request);
		}
		else {
			$tieBreakFlip = rand(1, 2);
			if($tieBreakFlip == 1) {
				$this->playerTurnOrder = 'first';			
				//add code to decide if skill, item or melee from enemy later
				return $this->exchangeMelee($request);
			}
			else {
				$this->playerTurnOrder = 'second';			
				//add code to decide if skill, item or melee from enemy later
				return $this->exchangeMelee($request);
			}	
		}	
	}
	
	public function exchangeMelee(Request $request)
	{
		$user = User::where('name', $request->user()->name)->first();
		$charObj = $user->character()->first();
		$enemyObj = GameActiveEnemy::where('id', $charObj->enemyId)->first();
		
		//distance calculation check
		$charRow = $charObj->mapPosition[0];
		$charColumn = $charObj->mapPosition[1];
		$enemyRow = $enemyObj->mapPosition[0];
		$enemyColumn = $enemyObj->mapPosition[1];
		$distance = sqrt((($enemyRow - $charRow) ** 2) + (($enemyColumn - $charColumn) ** 2));
		$decimalDistance = floor($distance);
		$fractionDistance = $distance - $decimalDistance;
		$finalDistance = 0;
		if($fractionDistance < .5)
			$finalDistance = floor($distance);
		else
			$finalDistance = ceil($distance);
			
		$playerValidRange = false;
		$enemyValidRange = false;
		
		if($finalDistance <= $charObj->combatRange) {
			$playerValidRange = true;
		}
		if($finalDistance <= $enemyObj->combatRange) {
			$enemyValidRange = true;
		}
		
		$playerChanceToHit = 0;	
		$playerAccuracyRoll = 0;
		$playerDamage = 0;
		$playerAttackSuccess = false;
		if($playerValidRange) {	
			$playerChanceToHit = $charObj->currentAccuracy - $enemyObj->currentDefense;	
			$playerAccuracyRoll = rand(1, 100);
			if($playerAccuracyRoll <= $playerChanceToHit) {
				$playerAttackSuccess = true;
				$playerDamage = $charObj->currentAttack * $charObj->attackMultiplier;
			}
		}
		
		$enemyChanceToHit = 0;	
		$enemyAccuracyRoll = 0;
		$enemyDamage = 0;
		$enemyAttackSuccess = false;
		if($enemyValidRange) {
			$enemyChanceToHit = $enemyObj->currentAccuracy - $charObj->currentDefense;
			$enemyAccuracyRoll = rand(1, 100);
			if($enemyAccuracyRoll <= $enemyChanceToHit) {
				$enemyAttackSuccess = true;
				$enemyDamage = $enemyObj->currentAttack * $enemyObj->attackMultiplier;
			}
		}
	
		if($this->playerTurnOrder == 'first' && $playerValidRange && $enemyValidRange) {
			$enemyObj->currentHealth = $enemyObj->currentHealth - ($playerDamage - $enemyObj->armour);
			$charObj->currentStamina = $charObj->currentStamina - $charObj->baseAttackCost;
			
			if($enemyObj->currentHealth <= 0) {
				$charObj->battle = false;
				if($charObj->currentTurn == $charObj->gameTurns)
					$charObj->currentTurn = 1;
				else
					$charObj->currentTurn = $charObj->currentTurn + 1;
				$enemyObj->save();
				$charObj->save();
				return response(['message' => 'Killed enemy with ' . $playerDamage . ' damage!',
				'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
				'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
				'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			}
			else {
				if($enemyDamage > 0)
					$charObj->currentHealth = $charObj->currentHealth - ($enemyDamage - $charObj->armour);
				$enemyObj->currentStamina = $enemyObj->currentStamina - $enemyObj->baseAttackCost;
				$enemyObj->save();
				$charObj->battle = false;
				if($charObj->currentTurn == $charObj->gameTurns)
					$charObj->currentTurn = 1;
				else
					$charObj->currentTurn = $charObj->currentTurn + 1;
				$charObj->save();
				
				if($charObj->currentHealth <= 0 && $playerAttackSuccess && $enemyAttackSuccess)
					return response(['message' => 'Dealt ' . $playerDamage . ' damage first but enemy killed you with ' . $enemyDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($charObj->currentHealth <= 0 && !$playerAttackSuccess && $enemyAttackSuccess)
					return response(['message' => 'You attacked first, missed and enemy killed you with ' . $enemyDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($charObj->currentHealth >= 0 && $playerAttackSuccess && !$enemyAttackSuccess)
					return response(['message' => 'Hit enemy first for ' . $playerDamage . ' damage, enemy missed with their attack.',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($charObj->currentHealth >= 0 && !$playerAttackSuccess && $enemyAttackSuccess)
					return response(['message' => 'Attacked enemy first but missed, enemy hits you for ' . $enemyDamage . '!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($charObj->currentHealth >= 0 && !$playerAttackSuccess && !$enemyAttackSuccess)
					return response(['message' => 'Both attacks missed!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			}		
		}
		else if($this->playerTurnOrder == 'second' && $playerValidRange && $enemyValidRange){
			$charObj->currentHealth = $charObj->currentHealth - ($enemyDamage - $charObj->armour);
			$enemyObj->currentStamina = $enemyObj->currentStamina - $enemyObj->baseAttackCost;
			
			//Log::debug($enemyObj->currentHealth); 
			//Log::debug($playerAttackSuccess); 
			//Log::debug($enemyAttackSuccess); 
			
			
			if($charObj->currentHealth <= 0) {	
				$enemyObj->save();
				$charObj->battle = false;
				if($charObj->currentTurn == $charObj->gameTurns)
					$charObj->currentTurn = 1;
				else
					$charObj->currentTurn = $charObj->currentTurn + 1;
				$charObj->save();
				return response(['message' => 'Enemy attacks first and killed you with ' . $enemyDamage . ' damage!',
				'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
				'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
				'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			}
			else {
				if($playerDamage > 0)
					$enemyObj->currentHealth = $enemyObj->currentHealth - ($playerDamage - $enemyObj->armour);
				$charObj->currentStamina = $charObj->currentStamina - $charObj->baseAttackCost;
				$enemyObj->save();
				$charObj->battle = false;
				if($charObj->currentTurn == $charObj->gameTurns)
					$charObj->currentTurn = 1;
				else
					$charObj->currentTurn = $charObj->currentTurn + 1;
				$charObj->save();
				
				/*
				if($enemyObj->currentHealth <= 0)
					return response(['message' => 'Killed enemy with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health, 'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			
				return response(['message' => 'Hit enemy second for ' . $playerDamage . ' damage and received ' . $enemyDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				*/
			
				if($enemyObj->currentHealth <= 0 && $playerAttackSuccess && $enemyAttackSuccess)
					return response(['message' => 'Enemy dealt ' . $enemyDamage . ' damage first but killed the enemy with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($enemyObj->currentHealth <= 0 && $playerAttackSuccess && !$enemyAttackSuccess)
					return response(['message' => 'Enemy attacked first, missed, and you killed the enemy with ' . $playerDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($enemyObj->currentHealth >= 0 && $playerAttackSuccess && !$enemyAttackSuccess)
					return response(['message' => 'Hit enemy after they missed you for ' . $playerDamage . '!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($enemyObj->currentHealth >= 0 && !$playerAttackSuccess && $enemyAttackSuccess)
					return response(['message' => 'Enemy hits you first for ' . $enemyDamage . ' damage and you missed with your attack!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($enemyObj->currentHealth >= 0 && !$playerAttackSuccess && !$enemyAttackSuccess)
					return response(['message' => 'Both attacks missed!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
				if($enemyObj->currentHealth >= 0 && $playerAttackSuccess && $enemyAttackSuccess)
					return response(['message' => 'Enemy hits you first for ' . $enemyDamage . ' damage and you attack for ' . $playerDamage . ' damage!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			}
		}
		else if(!$playerValidRange && $enemyValidRange){
			$charObj->currentHealth = $charObj->currentHealth - ($enemyDamage - $charObj->armour);
			$enemyObj->currentStamina = $enemyObj->currentStamina - $enemyObj->baseAttackCost;
			$enemyObj->save();
			$charObj->battle = false;
				if($charObj->currentTurn == $charObj->gameTurns)
					$charObj->currentTurn = 1;
				else
					$charObj->currentTurn = $charObj->currentTurn + 1;
			$charObj->save();
			
			if($charObj->currentHealth <= 0)	
				return response(['message' => 'Enemy killed you outside your range with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health, 'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			else
				return response(['message' => 'Enemy is out of range and attacked for ' . $enemyDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
		}
		else if($playerValidRange && !$enemyValidRange){
			$enemyObj->currentHealth = $enemyObj->currentHealth - ($playerDamage - $enemyObj->armour);
			$charObj->currentStamina = $charObj->currentStamina - $charObj->baseAttackCost;
			$enemyObj->save();
			$charObj->battle = false;
				if($charObj->currentTurn == $charObj->gameTurns)
					$charObj->currentTurn = 1;
				else
					$charObj->currentTurn = $charObj->currentTurn + 1;
			$charObj->save();
			
			if($enemyObj->currentHealth <= 0) {
				return response(['message' => 'Killed enemy from a safe distance with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health, 'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			}
			else
				return response(['message' => 'Attacked enemy from a safe distance and dealt ' . $playerDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
		}
		else {
			return response(['message' => 'Both combatants are not in range to fight!',
				'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
				'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
				'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
		}	
	}
}   