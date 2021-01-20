<?php

namespace Database\Factories;

use App\Models\Character;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Character::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(Faker $faker)
    {
        return [
			//rest are default valued in db migration
			'id' => 1,
			'raceId' => 1,
			'ownerUser' => 1,
			'charactername' => $faker->name,
			'gameClass' => 'warrior'
		];
    }
}
