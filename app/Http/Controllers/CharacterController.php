<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Log;

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
				$gameMap->tileset()->first()->delete();
				$gameMap->delete();
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

			
			if (!$user) {
				throw ValidationException::withMessages([
					'message' => ['database error, user does not exist.'],
				]);
			}			
				
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
}
?>