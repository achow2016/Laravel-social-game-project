<?php

namespace Database\Factories;
use App\Models\GameMap;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;
//use Illuminate\Support\Str;

class MapFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GameMap::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(Faker $faker)
    {
        return [
			'character_id' => 1,
			'startPoint' => [0,0],
			'endPoint' => [1,1]
        ];
    }
}