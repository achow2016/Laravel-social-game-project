<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameEnemiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('game_enemies');
        Schema::create('game_enemies', function (Blueprint $table) {
            $table->increments('id');
			$table->binary('avatar')->nullable();
			$table->string('gameRace');
			$table->string('name');
			$table->string('gameClass');
			$table->integer('mapId')->unsigned();
			$table->integer('health')->default('0');
			$table->integer('stamina')->default('0');
			$table->integer('accuracy')->default('1');
			$table->integer('attack')->default('0');
			$table->integer('baseAttackCost')->default('0');
			$table->integer('staminaRegen')->default('0');
			$table->integer('healthRegen')->default('0');
			$table->integer('agility')->default('0');
			$table->integer('money')->default('0');
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
        Schema::dropIfExists('game_enemies');
    }
}
