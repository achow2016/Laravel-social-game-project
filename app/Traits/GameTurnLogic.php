<?php 
namespace App\Traits;

use App\Models\Character;
use App\Models\User;
use App\Models\GameMap;
use App\Models\GameActiveEnemy;
use App\Models\ActiveGameCharacterItem;
use App\Models\GameItem;

use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

trait GameTurnLogic
{
	private $playerTurnOrder;
	
	//uses enemy id sent from calling function to drop specific enemy item onto a map square
	//should drop all equipment and inventory item as a single string to be processed by inspect function in game component
	public function dropEnemyLoot(Request $request, $enemyId) {
		$user = User::where('name', $request->user()->name)->first();
		$charObj = $user->character()->first();
		$existingMap = GameMap::where('id', $charObj->mapId)->first();
		
		//current enemy acting in this function, using request data
		$enemy = $existingMap->enemies()->get()->where('id', $enemyId)->first();
		$enemyItem = $enemy->items()->first();
		
		if($enemy->items()->first()) {
			$enemyMapPosition = $enemy->mapPosition;
			$targetItem = GameItem::where('id', $enemyItem->itemId)->first();
			//Log::debug($enemyItem);
			//Log::debug($targetItem);
			
			//gets map tileset as array for placing enemy data
			$map = $existingMap->tileset()->first()->pluck('mapData');
			$mapDecoded = json_decode($map[0], TRUE);
			$mapDecoded[$enemyMapPosition[0]][$enemyMapPosition[1]]['item'] = strval($targetItem->name);
			$tileSet = $existingMap->tileset()->first();
			$tileSet->mapData = $mapDecoded;
			$existingMap->tileset()->save($tileSet);
		}	
	}	
	
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

	//uses a player item and applies its effect
	public function usePlayerItem(Request $request)
	{
		try {
			$user = User::where('name', $request->user()->name)->first();
			$character = $user->character()->first();
			$itemName = $request->itemName;
			if($character) {
				
				//item matching, updates item quantity
				$ItemUsedData = GameItem::where('name', $itemName)->first();
				$characterItemUsed = $character->items()->get()->where('itemId', $ItemUsedData->id)->first();
				$characterItemUsed->quantity = $characterItemUsed->quantity - 1;
				if($characterItemUsed->quantity == 0)
					$character->items()->delete($characterItemUsed);
				else
					$character->items()->save($characterItemUsed);
				
				$matchFound = false;
				
				//if no effects starts a new array
				if($character->effects == null) {		
					$effects = [];
					$effect = (object) [
						'name' => $ItemUsedData->effect, 
						'effectStackAmount' => $ItemUsedData->effectStackAmount, 
						'effectStackLimit' => $ItemUsedData->effectStackLimit, 
						'effectPercent' => $ItemUsedData->effectPercent, 
						'effectDuration' => $ItemUsedData->effectDuration, 
						'effectDurationRemaining' => $ItemUsedData->effectDuration
					];
					$effects[] = $effect;
					$character->effects = $effects;
					$character->currentTurn = $character->currentTurn + 1;
					if($character->currentTurn > $character->gameTurns)
						$character->currentTurn = 1;
					$character->itemsUsed = $character->itemsUsed + 1;	
					$character->score = $character->score + 1;						
					$character->save();
					return (['message' => 'Used ' . $ItemUsedData->name . ', and gained ' . $ItemUsedData->effect . '.']);
				}
				//if there are effects in play, updates with conditions
				else {
					$effects = $character->effects;
					//checks for matches of same effects first
					foreach($effects as $effectIndex => $effect) {
						if($effect['name'] == $ItemUsedData->effect) {
							
							//replaces if new effect greater
							if($effect['effectPercent'] < $ItemUsedData->effectPercent) {
								$matchFound = true;
								unset($effects[$effectIndex]);
								$newEffect = (object) [
									'name' => $ItemUsedData->effect, 
									'effectStackAmount' => $ItemUsedData->effectStackAmount, 
									'effectStackLimit' => $ItemUsedData->effectStackLimit, 
									'effectPercent' => $ItemUsedData->effectPercent, 
									'effectDuration' => $ItemUsedData->effectDuration,
									'effectDurationRemaining' => $ItemUsedData->effectDuration
								];
								$effects[] = $newEffect;
								$orderedEffects = array_values($effects);
								$character->effects = $orderedEffects;
								$character->currentTurn = $character->currentTurn + 1;
								if($character->currentTurn > $character->gameTurns)
									$character->currentTurn = 1;
								$character->itemsUsed = $character->itemsUsed + 1;	
								$character->score = $character->score + 1;	
								$character->save();
								return (['message' => 'Used ' . $ItemUsedData->name . ' and increased a similar effect.']);
							}
							
							//increases stack count effect percentage is the same, and is below stack limit
							if($effect['effectPercent'] == $ItemUsedData->effectPercent
							&& $effect['effectStackAmount'] < $effect['effectStackLimit']) {
								$matchFound = true;
								$newStackCount = $effect['effectStackAmount'] + 1;
								unset($effects[$effectIndex]);
								$effect['effectStackAmount'] = $effect['effectStackAmount'] + 1;
								$newEffect = (object) [
									'name' => $ItemUsedData->effect, 
									'effectStackAmount' => $newStackCount,
									'effectStackLimit' => $ItemUsedData->effectStackLimit, 
									'effectPercent' => $ItemUsedData->effectPercent,
									'effectDuration' => $ItemUsedData->effectDuration,
									'effectDurationRemaining' => $ItemUsedData->effectDuration
								];
								$effects[] = $newEffect;
								$orderedEffects = array_values($effects);
								$character->effects = $orderedEffects;
								$character->currentTurn = $character->currentTurn + 1;
								if($character->currentTurn > $character->gameTurns)
									$character->currentTurn = 1;
								$character->itemsUsed = $character->itemsUsed + 1;	
								$character->score = $character->score + 1;	
								$character->save();
								return (['message' => 'Used ' . $ItemUsedData->name . ' and increased its effects.']);
							}
							
							//if same effect but stack limit hit, item is consumed but error returned
							if($effect['effectPercent'] == $ItemUsedData->effectPercent
							&& $effect['effectDuration'] <= $ItemUsedData->effectDuration
							&& $effect['effectStackAmount'] >= $effect['effectStackLimit']) {
								$matchFound = true;
								$character->currentTurn = $character->currentTurn + 1;
								if($character->currentTurn > $character->gameTurns)
									$character->currentTurn = 1;
								$character->itemsUsed = $character->itemsUsed + 1;	
								$character->score = $character->score + 1;	
								return (['message' => 'Used ' . $ItemUsedData->name . ', but it had no effect.']);
							}
						}
					}
					
					//adds new effect to array if others present but no matching
					if($matchFound == false && $character->effects != null) {
						$newEffect = (object) [
							'name' => $ItemUsedData->effect, 
							'effectStackAmount' => $ItemUsedData->effectStackAmount, 
							'effectStackLimit' => $ItemUsedData->effectStackLimit, 
							'effectPercent' => $ItemUsedData->effectPercent, 
							'effectDuration' => $ItemUsedData->effectDuration,
							'effectDurationRemaining' => $ItemUsedData->effectDuration
						];
						$effects[] = $newEffect;
						$orderedEffects = array_values($effects);
						$character->effects = $orderedEffects;
						$charObj->currentTurn = $charObj->currentTurn + 1;
						if($charObj->currentTurn > $charObj->gameTurns)
							$charObj->currentTurn = 1;
						$character->itemsUsed = $character->itemsUsed + 1;	
						$character->score = $character->score + 1;	
						$character->save();
						return (['message' => 'Used ' . $ItemUsedData->name . ', and gained ' . $ItemUsedData->effect . '.']);
					}	
				}	
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
	
	public function updateEffects(Request $request)
	{
		$user = User::where('name', $request->user()->name)->first();
		$charObj = $user->character()->first();
		$updates = [];
		$updatedEffects = [];
		$effects = $charObj->effects;
		if($effects != null)
		foreach($effects as $effectIndex => $effect) {
			//other effects here as they are added to game
			if($effect['name'] == 'Regen') {
				$regenRecovery = ($charObj->health * ($effect['effectPercent'] / 100)) * $effect['effectStackAmount'];
				if(($charObj->currentHealth + $regenRecovery) <= $charObj->health) {
					$charObj->currentHealth = $charObj->currentHealth + $regenRecovery;
					$updates[] = 'Regenerated ' . $regenRecovery . ' health.';
				}	
				else {
					$remainder = $charObj->health - $charObj->currentHealth;
					$charObj->currentHealth = $charObj->health;
					$updates[] = 'Regenerated ' . $remainder . ' health.';
				}
				$updatedEffect = (object) [
					'name' => $effect['name'], 
					'effectStackAmount' => $effect['effectStackAmount'], 
					'effectStackLimit' => $effect['effectStackLimit'], 
					'effectPercent' => $effect['effectPercent'], 
					'effectDuration' => $effect['effectDuration'],
					'effectDurationRemaining' => $effect['effectDurationRemaining'] - 1
				];
				$updatedEffects[] = $updatedEffect;			
			}
		}
		$charObj->effects = $updatedEffects;
		$charObj->save();
		return $updates;
		
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
		
		$enemyObj->currentStamina = $enemyObj->currentStamina - $enemyObj->baseAttackCost;
		$charObj->currentStamina = $charObj->currentStamina - $charObj->baseAttackCost;
		
		if($this->playerTurnOrder == 'first' && $playerValidRange && $enemyValidRange) {
			$charObj->battle = false;
			$charObj->currentTurn = $charObj->currentTurn + 1;
			if($charObj->currentTurn > $charObj->gameTurns)
				$charObj->currentTurn = 1;
			
			$enemyObj->currentHealth = $enemyObj->currentHealth - ($playerDamage - $enemyObj->armour);
			if($playerDamage > 0) {
				$charObj->damageDealt = $charObj->damageDealt + ($playerDamage - $enemyObj->armour);
				$charObj->score = $charObj->score + $charObj->damageDealt;	
			}
			if($enemyObj->currentHealth <= 0) {
				$this->dropEnemyLoot($request, $enemyObj->id);
				$enemyObj->save();
				$charObj->save();
				return (['message' => 'Killed enemy with ' . $playerDamage . ' damage!',
				'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
				'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
				'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
				'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
			}
			else {
				$charObj->currentHealth = $charObj->currentHealth - ($enemyDamage - $charObj->armour);
				if($enemyDamage > 0) {
					$charObj->damageReceived = $charObj->damageReceived + ($enemyDamage - $charObj->armour);			
					$charObj->score = $charObj->score + $charObj->damageReceived;	
				}
				$enemyObj->save();
				$charObj->save();
				if($charObj->currentHealth >= 0 && $playerAttackSuccess && $enemyAttackSuccess)
					return (['message' => 'Dealt ' . $playerDamage . ' damage first and enemy hits you for ' . $enemyDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				if($charObj->currentHealth <= 0 && $playerAttackSuccess && $enemyAttackSuccess)
					return (['message' => 'Dealt ' . $playerDamage . ' damage first but enemy killed you with ' . $enemyDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				if($charObj->currentHealth <= 0 && !$playerAttackSuccess && $enemyAttackSuccess)
					return (['message' => 'You attacked first, missed and enemy killed you with ' . $enemyDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				if($charObj->currentHealth >= 0 && $playerAttackSuccess && !$enemyAttackSuccess)
					return (['message' => 'Hit enemy first for ' . $playerDamage . ' damage, enemy missed with their attack.',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
						'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				if($charObj->currentHealth >= 0 && !$playerAttackSuccess && $enemyAttackSuccess)
					return (['message' => 'Attacked enemy first but missed, enemy hits you for ' . $enemyDamage . '!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
						'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				if($charObj->currentHealth >= 0 && !$playerAttackSuccess && !$enemyAttackSuccess)
					return (['message' => 'Both attacks missed!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
						'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
			}		
		}
		else if($this->playerTurnOrder == 'second' && $playerValidRange && $enemyValidRange){
			$charObj->currentHealth = $charObj->currentHealth - ($enemyDamage - $charObj->armour);
			if($enemyDamage > 0) {
				$charObj->damageReceived = $charObj->damageReceived + ($enemyDamage - $charObj->armour);			
				$charObj->score = $charObj->score + $charObj->damageReceived;			
			}
			$charObj->save();
			
			$charObj->battle = false;
			$charObj->currentTurn = $charObj->currentTurn + 1;
			if($charObj->currentTurn > $charObj->gameTurns)
				$charObj->currentTurn = 1;
					
			//Log::debug($enemyObj->currentHealth); 
			//Log::debug($playerAttackSuccess); 
			//Log::debug($enemyAttackSuccess); 
			
			
			if($charObj->currentHealth <= 0) {	
				$enemyObj->save();
				return (['message' => 'Enemy attacks first and killed you with ' . $enemyDamage . ' damage!',
				'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
				'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
				'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
				'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
			}
			else {
				$enemyObj->currentHealth = $enemyObj->currentHealth - ($playerDamage - $enemyObj->armour);
				if($playerDamage > 0) {
					$charObj->damageDealt = $charObj->damageDealt + ($playerDamage - $enemyObj->armour);
					$charObj->score = $charObj->score + $charObj->damageDealt;	
				}
				
				//$enemyObj->save();
				$charObj->save();
				
				if($enemyObj->currentHealth <= 0 && $playerAttackSuccess && $enemyAttackSuccess) {
					$this->dropEnemyLoot($request, $enemyObj->id);
					$enemyObj->save();
					return (['message' => 'Enemy dealt ' . $enemyDamage . ' damage first but killed the enemy with ' . $playerDamage . ' damage!', 'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);	
				}
				if($enemyObj->currentHealth <= 0 && $playerAttackSuccess && !$enemyAttackSuccess) {
					$this->dropEnemyLoot($request, $enemyObj->id);
					$enemyObj->save();					
					return (['message' => 'Enemy attacked first, missed, and you killed the enemy with ' . $playerDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				}	
				if($enemyObj->currentHealth >= 0 && $playerAttackSuccess && !$enemyAttackSuccess) {
					$enemyObj->save();
					return (['message' => 'Hit enemy after they missed you for ' . $playerDamage . '!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
						'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				}		
				if($enemyObj->currentHealth >= 0 && !$playerAttackSuccess && $enemyAttackSuccess) {
					$enemyObj->save();
					return (['message' => 'Enemy hits you first for ' . $enemyDamage . ' damage and you missed with your attack!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
						'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				}	
				if($enemyObj->currentHealth >= 0 && !$playerAttackSuccess && !$enemyAttackSuccess) {
					$enemyObj->save();
					return (['message' => 'Both attacks missed!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
						'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				}		
				if($enemyObj->currentHealth >= 0 && $playerAttackSuccess && $enemyAttackSuccess) {
					$enemyObj->save();
					return (['message' => 'Enemy hits you first for ' . $enemyDamage . ' damage and you attack for ' . $playerDamage . ' damage!',
						'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
						'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
						'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
						'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
				}		
			}
		}
		else if(!$playerValidRange && $enemyValidRange){
			$charObj->currentHealth = $charObj->currentHealth - ($enemyDamage - $charObj->armour);
			if($enemyDamage > 0) {
				$charObj->damageReceived = $charObj->damageReceived + ($enemyDamage - $charObj->armour);			
				$charObj->score = $charObj->score + $charObj->damageReceived;
			}
			$charObj->battle = false;
			$charObj->currentTurn = $charObj->currentTurn + 1;
			if($charObj->currentTurn > $charObj->gameTurns)
				$charObj->currentTurn = 1;
			$charObj->save();
			$enemyObj->save();
			
			if($charObj->currentHealth <= 0)	
				return (['message' => 'Enemy killed you outside your range with ' . $playerDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
			else
				return (['message' => 'Enemy is out of range and attacked for ' . $enemyDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
		}
		else if($playerValidRange && !$enemyValidRange){
			$enemyObj->currentHealth = $enemyObj->currentHealth - ($playerDamage - $enemyObj->armour);
			if($playerDamage > 0) {
				$charObj->damageDealt = $charObj->damageDealt + ($playerDamage - $enemyObj->armour);
				$charObj->score = $charObj->score + $charObj->damageDealt;
			}	
			$charObj->battle = false;
			$charObj->currentTurn = $charObj->currentTurn + 1;
			if($charObj->currentTurn > $charObj->gameTurns)
				$charObj->currentTurn = 1;
			$charObj->save();
			
			if($enemyObj->currentHealth <= 0) {
				$this->dropEnemyLoot($request, $enemyObj->id);
				$enemyObj->save();
				return (['message' => 'Killed enemy from a safe distance with ' . $playerDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
			}
			else
				return (['message' => 'Attacked enemy from a safe distance and dealt ' . $playerDamage . ' damage!',
					'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
					'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
					'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
					'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
			$enemyObj->save();		
		}
		else {
			$charObj->battle = false;
			$charObj->currentTurn = $charObj->currentTurn + 1;
			if($charObj->currentTurn > $charObj->gameTurns)
				$charObj->currentTurn = 1;
			$charObj->save();
			$enemyObj->save();
			
			return (['message' => 'Both combatants are not in range to fight!',
				'enemyNewHealth' => $enemyObj->currentHealth . '/' . $enemyObj->health,
				'playerNewHealth' => $charObj->currentHealth . '/' . $charObj->health,
				'playerNewStamina' => $charObj->currentStamina . '/' . $charObj->stamina,
				'enemyNewStamina' => $enemyObj->currentStamina . '/' . $enemyObj->stamina]);
		}	
	}
}   