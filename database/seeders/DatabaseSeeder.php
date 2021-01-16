<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
//use Illuminate\Support\Facades\File;
//use Illuminate\Support\Facades\Storage;
use App\Models\CharacterRace;
use App\Models\CashShopItem;
//use Symfony\Component\Console\Output\ConsoleOutput;

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
			$characterRace->save();	
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
		
		//$output = new ConsoleOutput();
		//$output->writeln(var_dump($data['race'][0]['race']));
    }
}
