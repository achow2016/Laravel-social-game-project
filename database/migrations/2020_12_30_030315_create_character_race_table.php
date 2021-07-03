<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterRaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_races', function (Blueprint $table) {
            $table->increments('id');
			$table->string('race')->unique();
			$table->integer('attack');
			$table->integer('health');
			$table->integer('healthRegen');
			$table->integer('stamina');
			$table->integer('staminaRegen');
			$table->integer('agility');
			$table->integer('sightRange');
			$table->json('weaknesses')->nullable();
			$table->json('resistances')->nullable();
			$table->binary('avatar')->nullable();
			$table->binary('meleeAnimation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_races');
    }
}
