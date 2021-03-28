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
	
	public function findBattleTurnOrder(Request $request)     
	{               
		$user = User::where('name', $request->user()->name)->first();
		$charObj = $user->character()->first();
		$enemyObj = GameActiveEnemy::where('id', $charObj->enemyId)->first();
		if($charObj->agility < $enemyObj->agility) {
			$this->playerTurnOrder = 'second';
			
			//add code to decide if skill, item or melee from enemy later
			//$this->enemyMeleePlayer();
			
			//$this->playerMeleeEnemy();
			//$this->returnMeleeExchangeResult();
			return $this->exchangeMelee($request);
		}
		else {
			$this->playerTurnOrder = 'first';
			
			//add code to decide if skill, item or melee from enemy later
			//$this->enemyMeleePlayer();
			//$this->playerMeleeEnemy();
			
			return $this->exchangeMelee($request);
		}
	}
	
	public function exchangeMelee(Request $request)
	{
		$user = User::where('name', $request->user()->name)->first();
		$charObj = $user->character()->first();
		$enemyObj = GameActiveEnemy::where('id', $charObj->enemyId)->first();
		$playerChanceToHit = $charObj->currentAccuracy - $enemyObj->currentDefense;
		$enemyChanceToHit = $enemyObj->currentAccuracy - $charObj->currentDefense;
		$playerAccuracyRoll = rand(1, 100);
		$enemyAccuracyRoll = rand(1, 100);
		$playerDamage = 0;
		$enemyDamage = 0;
		
		if($playerAccuracyRoll <= $playerChanceToHit) {
			$playerDamage = $charObj->currentAttack * $charObj->attackMultiplier;
		}
		if($enemyAccuracyRoll <= $enemyChanceToHit) {
			$enemyDamage = $enemyObj->currentAttack * $enemyObj->attackMultiplier;
		}
		
		Log::debug($enemyAccuracyRoll); 
		Log::debug($enemyChanceToHit); 
		Log::debug($enemyDamage); 
		Log::debug($enemyObj->currentAccuracy); 
		Log::debug($enemyObj->currentAttack); 
		Log::debug($enemyObj->attackMultiplier); 
		
		if($this->playerTurnOrder == 'first') {
			$enemyObj->currentHealth = $enemyObj->currentHealth - ($playerDamage - $enemyObj->armour);
			$charObj->currentStamina = $charObj->currentStamina - $charObj->baseAttackCost;
			
			if($enemyObj->currentHealth <= 0) {
				return response(['message' => 'Killed enemy with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health, 'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			}
			
			$charObj->currentHealth = $charObj->currentHealth - ($enemyDamage - $charObj->armour);
			$enemyObj->currentStamina = $enemyObj->currentStamina - $enemyObj->baseAttackCost;
			$enemyObj->save();
			$charObj->save();
			
			if($charObj->currentHealth <= 0)
				return response(['message' => 'Enemy killed you with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health, 'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			
			return response(['message' => 'Hit enemy first for ' . $playerDamage . ' damage and received ' . $enemyDamage . ' damage!',
				'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
				'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
				'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
		}
		else {
			$playerObj->currentHealth = $playerObj->currentHealth - ($enemyDamage - $playerObj->armour);
			$enemyObj->currentStamina = $enemyObj->currentStamina - $enemyObj->baseAttackCost;
			
			if($playerObj->currentHealth <= 0)	
				return response(['message' => 'Enemy killed you with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health, 'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
			
			$enemyObj->currentHealth = $enemyObj->currentHealth - ($playerDamage - $enemyObj->armour);
			$charObj->currentStamina = $charObj->currentStamina - $charObj->baseAttackCost;
			$enemyObj->save();
			$charObj->save();
			
			if($enemyObj->currentHealth <= 0)
				return response(['message' => 'Killed enemy with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health, 'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
		
			return response(['message' => 'Hit enemy second for ' . $playerDamage . ' damage and received ' . $enemyDamage . ' damage!',
				'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
				'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
				'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina], 200);
		}	
	}
}   