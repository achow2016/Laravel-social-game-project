<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//models
use App\Models\Character;
use App\Models\CharacterRace;
use App\Models\CharacterClass;
use App\Models\User;
//use App\Models\GameMap;
//use App\Models\GameMapTileset;
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
//use App\Traits\GameTurnLogic;

class ScoreController extends Controller {
	
	//use GameTurnLogic;

	public function getCharacterScoreDetails(Request $request) 
	{
		try {
			$user = User::where('name', $request->ownerUser)->first();
			$details = GameScoreRecord::where('userName', $user->name)->first();
			if($details) {
				return response(['details' => $details], 200);
			}
			else {
				return response(['message' => 'Character not found.'], 200);
			}	
		}
		catch(Throwable $e) {
			report($e);
			return response(['status' => 'Character could not be created. Please report to admin.'], 422);
		}
	}
	
	//gets game character avatar
	public function getCharacterScoreAvatar(Request $request) 
	{
		try {
			$record = GameScoreRecord::where('characterName', $request->characterName)->first();
			
			return response([
				'charAvatar' => $record->avatar
			], 200);
			
		}
		catch(Throwable $e) {
			report($e);
			return response(['message' => 'game state data list could not be made. Please report to admin.'], 422);
		}
	}

}
?>