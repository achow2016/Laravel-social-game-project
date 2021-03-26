<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
//use Illuminate\Support\Facades\File;
//use Illuminate\Support\Facades\Storage;
use App\Models\CharacterRace;
use App\Models\CharacterClass;
use App\Models\GameEnemy;
use App\Models\CashShopItem;
use App\Models\Post;

use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Str;

use App\Models\GameItem;
use App\Models\GameOffhand;
use App\Models\GameWeapon;
use App\Models\GameArmsEquipment;
use App\Models\GameBodyEquipment;
use App\Models\GameHeadEquipment;
use App\Models\GameLegEquipment;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		//factories
		// \App\Models\User::factory()->create();
        //\App\Models\Secret::factory()->create();
        // \App\Models\User::factory(10)->create();
		
		//game data tables
		
		//character races
		
		$raceJsonPath = public_path() . '\json\CharacterRace.json';
		$data = file_get_contents($raceJsonPath);
		$data = json_decode($data, true);
		
		foreach ($data['race'] as $item) {            
			$characterRace = new CharacterRace();
			$characterRace->setAttribute('race', $item['race']);
			$characterRace->setAttribute('attack', $item['attack']);
			$characterRace->setAttribute('health', $item['health']);
			$characterRace->setAttribute('healthRegen', $item['healthRegen']);
			$characterRace->setAttribute('stamina', $item['stamina']);
			$characterRace->setAttribute('staminaRegen', $item['staminaRegen']);
			$characterRace->setAttribute('agility', $item['agility']);
			$characterRace->setAttribute('avatar', $item['avatar']);
			$characterRace->setAttribute('meleeAnimation', $item['meleeAnimation']);
			$characterRace->save();	
		}
		
		//character classes
		
		$raceJsonPath = public_path() . '\json\CharacterClass.json';
		$data = file_get_contents($raceJsonPath);
		$data = json_decode($data, true);
		
		foreach ($data['class'] as $item) {            
			$characterClass = new CharacterClass();
			$characterClass->setAttribute('name', $item['name']);
			$characterClass->setAttribute('resistances', $item['resistances']);
			$characterClass->setAttribute('weaknesses', $item['weaknesses']);
			$characterClass->setAttribute('attack', $item['attack']);
			$characterClass->setAttribute('health', $item['health']);
			$characterClass->setAttribute('healthRegen', $item['healthRegen']);
			$characterClass->setAttribute('stamina', $item['stamina']);
			$characterClass->setAttribute('staminaRegen', $item['staminaRegen']);
			$characterClass->setAttribute('defense', $item['defense']);
			$characterClass->setAttribute('baseAttackCost', $item['baseAttackCost']);			
			$characterClass->setAttribute('skills', $item['skills']);
			$characterClass->setAttribute('agility', $item['agility']);
			$characterClass->save();	
		}
				
		//shop inventory
		$inventoryJsonPath = public_path() . '\json\StoreItems.json';
		$data = file_get_contents($inventoryJsonPath);
		$data = json_decode($data, true);
		
		foreach ($data['inventory'] as $item) {            
			$cashShopItem = new CashShopItem();
			$cashShopItem->setAttribute('name', $item['name']);
			$cashShopItem->setAttribute('itemDescription', $item['itemDescription']);
			$cashShopItem->setAttribute('type', $item['type']);
			$cashShopItem->setAttribute('quantity', $item['quantity']);
			if(array_key_exists("duration",$item))
				$cashShopItem->setAttribute('duration', $item['duration']);	
			$cashShopItem->setAttribute('cost', $item['cost']);
			$cashShopItem->save();	
		}
		
		//game items
		$itemsJsonPath = public_path() . '\json\GameItems.json';
		$data = file_get_contents($itemsJsonPath);
		$data = json_decode($data, true);
		
		foreach ($data['items'] as $item) {            
			$gameItem = new GameItem();
			$gameItem->setAttribute('name', $item['name']);
			$gameItem->setAttribute('description', $item['description']);
			$gameItem->setAttribute('effect', $item['effect']);
			$gameItem->setAttribute('effectStackAmount', $item['effectStackAmount']);
			$gameItem->setAttribute('effectStackLimit', $item['effectStackLimit']);
			$gameItem->setAttribute('effectPercent', $item['effectPercent']);
			$gameItem->setAttribute('effectDuration', $item['effectDuration']);
			$gameItem->setAttribute('shopValue', $item['shopValue']);
			$gameItem->save();	
		}
		
		//game offhand equipment
		$offhandJsonPath = public_path() . '\json\GameOffhandItems.json';
		$data = file_get_contents($offhandJsonPath);
		$data = json_decode($data, true);
		foreach ($data['items'] as $item) {            
			$gameOffhand = new GameOffhand();
			$gameOffhand->setAttribute('name', $item['name']);
			$gameOffhand->setAttribute('image', $item['image']);
			$gameOffhand->setAttribute('gameLevel', $item['gameLevel']);
			$gameOffhand->setAttribute('description', $item['description']);
			$gameOffhand->setAttribute('attack', $item['attack']);
			$gameOffhand->setAttribute('armour', $item['armour']);
			$gameOffhand->setAttribute('defense', $item['defense']);
			$gameOffhand->setAttribute('cost', $item['cost']);
			$gameOffhand->save();	
		}
		
		//game weapon equipment
		$weaponsJsonPath = public_path() . '\json\GameWeapons.json';
		$data = file_get_contents($weaponsJsonPath);
		$data = json_decode($data, true);
		foreach ($data['weapons'] as $item) {            
			$gameWeapon = new GameWeapon();
			$gameWeapon->setAttribute('name', $item['name']);
			$gameWeapon->setAttribute('image', $item['image']);
			$gameWeapon->setAttribute('gameLevel', $item['gameLevel']);
			$gameWeapon->setAttribute('description', $item['description']);
			$gameWeapon->setAttribute('attack', $item['attack']);
			$gameWeapon->setAttribute('armour', $item['armour']);
			$gameWeapon->setAttribute('defense', $item['defense']);
			$gameWeapon->setAttribute('cost', $item['cost']);
			$gameWeapon->save();	
		}

		//game leg equipment
		$legEquipmentJsonPath = public_path() . '\json\GameLegEquipment.json';
		$data = file_get_contents($legEquipmentJsonPath);
		$data = json_decode($data, true);
		foreach ($data['legEquipment'] as $item) {            
			$gameLegEquipment = new GameLegEquipment();
			$gameLegEquipment->setAttribute('name', $item['name']);
			$gameLegEquipment->setAttribute('image', $item['image']);
			$gameLegEquipment->setAttribute('gameLevel', $item['gameLevel']);
			$gameLegEquipment->setAttribute('description', $item['description']);
			$gameLegEquipment->setAttribute('attack', $item['attack']);
			$gameLegEquipment->setAttribute('armour', $item['armour']);
			$gameLegEquipment->setAttribute('defense', $item['defense']);
			$gameLegEquipment->setAttribute('cost', $item['cost']);
			$gameLegEquipment->save();	
		}
	
		//game head equipment
		$headEquipmentJsonPath = public_path() . '\json\GameHeadEquipment.json';
		$data = file_get_contents($headEquipmentJsonPath);
		$data = json_decode($data, true);
		foreach ($data['headEquipment'] as $item) {            
			$gameHeadEquipment = new GameHeadEquipment();
			$gameHeadEquipment->setAttribute('name', $item['name']);
			$gameHeadEquipment->setAttribute('image', $item['image']);
			$gameHeadEquipment->setAttribute('gameLevel', $item['gameLevel']);
			$gameHeadEquipment->setAttribute('description', $item['description']);
			$gameHeadEquipment->setAttribute('attack', $item['attack']);
			$gameHeadEquipment->setAttribute('armour', $item['armour']);
			$gameHeadEquipment->setAttribute('defense', $item['defense']);
			$gameHeadEquipment->setAttribute('cost', $item['cost']);
			$gameHeadEquipment->save();	
		}
	
		//game body equipment
		$bodyEquipmentJsonPath = public_path() . '\json\GameBodyEquipment.json';
		$data = file_get_contents($bodyEquipmentJsonPath);
		$data = json_decode($data, true);
		foreach ($data['bodyEquipment'] as $item) {            
			$gameBodyEquipment = new GameBodyEquipment();
			$gameBodyEquipment->setAttribute('name', $item['name']);
			$gameBodyEquipment->setAttribute('image', $item['image']);
			$gameBodyEquipment->setAttribute('gameLevel', $item['gameLevel']);
			$gameBodyEquipment->setAttribute('description', $item['description']);
			$gameBodyEquipment->setAttribute('attack', $item['attack']);
			$gameBodyEquipment->setAttribute('armour', $item['armour']);
			$gameBodyEquipment->setAttribute('defense', $item['defense']);
			$gameBodyEquipment->setAttribute('cost', $item['cost']);
			$gameBodyEquipment->save();	
		}
		
		//game arms equipment
		$armsEquipmentJsonPath = public_path() . '\json\GameArmsEquipment.json';
		$data = file_get_contents($armsEquipmentJsonPath);
		$data = json_decode($data, true);
		foreach ($data['armsEquipment'] as $item) {            
			$gameArmsEquipment = new GameArmsEquipment();
			$gameArmsEquipment->setAttribute('name', $item['name']);
			$gameArmsEquipment->setAttribute('image', $item['image']);
			$gameArmsEquipment->setAttribute('gameLevel', $item['gameLevel']);
			$gameArmsEquipment->setAttribute('description', $item['description']);
			$gameArmsEquipment->setAttribute('attack', $item['attack']);
			$gameArmsEquipment->setAttribute('armour', $item['armour']);
			$gameArmsEquipment->setAttribute('defense', $item['defense']);
			$gameArmsEquipment->setAttribute('cost', $item['cost']);
			$gameArmsEquipment->save();	
		}
	
				//enemies
		
		//$data = null;
		$enemiesJsonPath = public_path() . '\json\Enemies.json';
		$data = file_get_contents($enemiesJsonPath);
		//error_log($data);
		$data = json_decode($data, true);
		
		foreach ($data['enemy'] as $item) {            
			$enemy = new GameEnemy();
			$enemy->setAttribute('name', $item['name']);
			$enemy->setAttribute('gameLevel', $item['gameLevel']);
			$enemy->setAttribute('gameRace', $item['gameRace']);
			$enemy->setAttribute('gameClass', $item['gameClass']);
			$enemy->setAttribute('money', $item['money']);
			$enemy->setAttribute('itemLootInventory', $item['itemLootInventory']);
			$enemy->setAttribute('avatar', $item['avatar']);
			$enemy->setAttribute('weapon', $item['weapon']);
			$enemy->setAttribute('offHand', $item['offHand']);
			$enemy->setAttribute('bodyEquipment', $item['bodyEquipment']);
			$enemy->setAttribute('headEquipment', $item['headEquipment']);
			$enemy->setAttribute('armsEquipment', $item['armsEquipment']);
			$enemy->setAttribute('legsEquipment', $item['legsEquipment']);
			$enemy->save();	
		}
	
		//$output = new ConsoleOutput();
		//$output->writeln(var_dump($data['race'][0]['race']));
		
		/*
		for($i = 0; $i < 100; $i++) {            
			$post = new Post();
			$post->setAttribute('user_id', 1);
			$post->setAttribute('name', Str::random(12));
			$post->setAttribute('postText', Str::random(20));
			$post->setAttribute('date', date('now'));
			$post->save();

		}
		*/
	}
}
